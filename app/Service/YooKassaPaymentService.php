<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class YooKassaPaymentService
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    public function createPaymentForOrder(Order $order): OrderPayment
    {
        if ($order->payment_method !== Order::PAYMENT_METHOD_YOOKASSA) {
            throw ValidationException::withMessages([
                'payment_method' => 'Для этого заказа не требуется онлайн-оплата.',
            ]);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Ссылку на оплату можно создать только для заказа в статусе ожидания.',
            ]);
        }

        $order->loadMissing(['items.product', 'user']);

        $idempotenceKey = (string) Str::uuid();
        $requestPayload = [
            'amount' => [
                'value' => $this->formatAmount($order->total),
                'currency' => config('services.yookassa.currency', 'RUB'),
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('payments.yookassa.return', ['order' => $order]),
            ],
            'description' => sprintf('Оплата заказа #%d', $order->id),
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
        ];

        if ($this->receiptsEnabled() && $this->receiptMode() === 'embedded') {
            $requestPayload['receipt'] = $this->buildEmbeddedReceiptPayload($order);
        }

        $payment = $order->payments()->create([
            'provider' => OrderPayment::PROVIDER_YOOKASSA,
            'status' => OrderPayment::STATUS_PENDING,
            'amount' => $requestPayload['amount']['value'],
            'currency' => $requestPayload['amount']['currency'],
            'idempotence_key' => $idempotenceKey,
            'request_payload' => $requestPayload,
        ]);

        try {
            $responsePayload = $this->api()
                ->withHeaders([
                    'Idempotence-Key' => $idempotenceKey,
                ])
                ->post('/payments', $requestPayload)
                ->throw()
                ->json();
        } catch (Throwable $exception) {
            $payment->error_message = $exception->getMessage();
            $payment->save();

            throw $exception;
        }

        $payment = $this->fillPaymentFromRemotePayload($payment, $responsePayload);

        if ($this->receiptsEnabled() && $this->receiptMode() === 'embedded') {
            $this->createEmbeddedReceiptRecord($payment, $requestPayload, $responsePayload);
        }

        return $payment;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function handleWebhook(array $payload): void
    {
        $externalPaymentId = Arr::get($payload, 'object.id');
        if (!is_string($externalPaymentId) || $externalPaymentId === '') {
            return;
        }

        $payment = OrderPayment::query()
            ->with(['order', 'receipts'])
            ->where('provider', OrderPayment::PROVIDER_YOOKASSA)
            ->where('external_payment_id', $externalPaymentId)
            ->latest('id')
            ->first();

        if (!$payment) {
            return;
        }

        $remotePayload = $this->fetchPayment($externalPaymentId);
        $this->synchronizePayment($payment, $remotePayload);
    }

    public function synchronizePayment(OrderPayment $payment, ?array $remotePayload = null): OrderPayment
    {
        if ($payment->external_payment_id === null) {
            return $payment;
        }

        $remotePayload ??= $this->fetchPayment($payment->external_payment_id);
        $payment = $this->fillPaymentFromRemotePayload($payment, $remotePayload);

        $payment->loadMissing(['order.items.product', 'order.user', 'receipts']);

        if ($payment->status === OrderPayment::STATUS_SUCCEEDED && $payment->order->status === Order::STATUS_PENDING) {
            $this->orderService->markAsPaid($payment->order);
            $payment->order->refresh();
        }

        if (
            $payment->status === OrderPayment::STATUS_SUCCEEDED
            && config('services.yookassa.receipts.enabled', false)
            && $this->receiptMode() === 'separate'
            && !$payment->receipts()->exists()
        ) {
            $this->createReceipt($payment);
        }

        if ($this->receiptsEnabled() && $this->receiptMode() === 'embedded') {
            $this->updateEmbeddedReceiptRecord($payment, $remotePayload);
        }

        return $payment->fresh(['order', 'latestReceipt']);
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchPayment(string $externalPaymentId): array
    {
        return $this->api()
            ->get('/payments/' . $externalPaymentId)
            ->throw()
            ->json();
    }

    public function createReceipt(OrderPayment $payment): PaymentReceipt
    {
        $payment->loadMissing(['order.items.product', 'order.user']);

        $requestPayload = $this->buildReceiptPayload($payment);

        $receipt = $payment->receipts()->create([
            'provider' => OrderPayment::PROVIDER_YOOKASSA,
            'type' => PaymentReceipt::TYPE_PAYMENT,
            'status' => PaymentReceipt::STATUS_PENDING,
            'send_to_customer' => (bool) $requestPayload['send'],
            'request_payload' => $requestPayload,
        ]);

        try {
            $responsePayload = $this->api()
                ->withHeaders([
                    'Idempotence-Key' => (string) Str::uuid(),
                ])
                ->post('/receipts', $requestPayload)
                ->throw()
                ->json();
        } catch (Throwable $exception) {
            $receipt->error_message = $exception->getMessage();
            $receipt->save();

            throw $exception;
        }

        $receipt->external_receipt_id = Arr::get($responsePayload, 'id');
        $receipt->status = Arr::get($responsePayload, 'status', PaymentReceipt::STATUS_PENDING);
        $receipt->response_payload = $responsePayload;
        $receipt->save();

        return $receipt;
    }

    /**
     * @param array<string, mixed> $remotePayload
     */
    private function fillPaymentFromRemotePayload(OrderPayment $payment, array $remotePayload): OrderPayment
    {
        $status = (string) Arr::get($remotePayload, 'status', OrderPayment::STATUS_PENDING);

        $payment->status = $status;
        $payment->external_payment_id = Arr::get($remotePayload, 'id');
        $payment->confirmation_url = Arr::get($remotePayload, 'confirmation.confirmation_url');
        $payment->response_payload = $remotePayload;
        $payment->error_message = null;

        if ($status === OrderPayment::STATUS_SUCCEEDED && $payment->paid_at === null) {
            $payment->paid_at = now();
        }

        if ($status === OrderPayment::STATUS_CANCELED && $payment->canceled_at === null) {
            $payment->canceled_at = now();
        }

        $payment->save();

        return $payment;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildReceiptPayload(OrderPayment $payment): array
    {
        $order = $payment->order;

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'description' => Str::limit($item->product?->name ?? ('Товар #' . $item->product_id), 128, ''),
                'quantity' => (float) $item->quantity,
                'amount' => [
                    'value' => $this->formatAmount($item->price),
                    'currency' => config('services.yookassa.currency', 'RUB'),
                ],
                'vat_code' => (int) config('services.yookassa.receipts.vat_code'),
                'payment_mode' => (string) config('services.yookassa.receipts.payment_mode'),
                'payment_subject' => (string) config('services.yookassa.receipts.payment_subject'),
            ];
        }

        $customer = $this->buildReceiptCustomer($order);

        return array_filter([
            'customer' => $customer,
            'payment_id' => $payment->external_payment_id,
            'type' => PaymentReceipt::TYPE_PAYMENT,
            'send' => array_key_exists('email', $customer),
            'items' => $items,
            'tax_system_code' => config('services.yookassa.receipts.tax_system_code'),
            'settlements' => [
                [
                    'type' => (string) config('services.yookassa.receipts.settlement_type'),
                    'amount' => [
                        'value' => $this->formatAmount($payment->amount),
                        'currency' => $payment->currency,
                    ],
                ],
            ],
            'internet' => true,
        ], static fn ($value) => $value !== null);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEmbeddedReceiptPayload(Order $order): array
    {
        $order->loadMissing(['items.product', 'user']);

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'description' => Str::limit($item->product?->name ?? ('Товар #' . $item->product_id), 128, ''),
                'quantity' => (float) $item->quantity,
                'amount' => [
                    'value' => $this->formatAmount($item->price),
                    'currency' => config('services.yookassa.currency', 'RUB'),
                ],
                'vat_code' => (int) config('services.yookassa.receipts.vat_code'),
                'payment_mode' => (string) config('services.yookassa.receipts.payment_mode'),
                'payment_subject' => (string) config('services.yookassa.receipts.payment_subject'),
            ];
        }

        return [
            'customer' => $this->buildReceiptCustomer($order),
            'items' => $items,
            'internet' => true,
            'tax_system_code' => config('services.yookassa.receipts.tax_system_code'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function buildReceiptCustomer(Order $order): array
    {
        $user = $order->user;
        $email = trim((string) $user->email);
        $phone = $this->normalizePhone($user->phone);

        $customer = array_filter([
            'full_name' => trim($user->full_name),
            'email' => $email !== '' ? $email : null,
            'phone' => $phone !== '' ? $phone : null,
        ]);

        if (!array_key_exists('email', $customer) && !array_key_exists('phone', $customer)) {
            throw ValidationException::withMessages([
                'receipt' => 'Для формирования чека YooKassa нужен email или телефон покупателя.',
            ]);
        }

        return $customer;
    }

    /**
     * @param array<string, mixed> $requestPayload
     * @param array<string, mixed> $responsePayload
     */
    private function createEmbeddedReceiptRecord(OrderPayment $payment, array $requestPayload, array $responsePayload): void
    {
        $status = Arr::get($responsePayload, 'receipt_registration', PaymentReceipt::STATUS_REGISTERED);

        $payment->receipts()->updateOrCreate(
            [
                'provider' => OrderPayment::PROVIDER_YOOKASSA,
                'type' => PaymentReceipt::TYPE_PAYMENT,
            ],
            [
                'status' => is_string($status) ? $status : PaymentReceipt::STATUS_REGISTERED,
                'send_to_customer' => array_key_exists('email', Arr::get($requestPayload, 'receipt.customer', [])),
                'request_payload' => Arr::get($requestPayload, 'receipt'),
                'response_payload' => [
                    'receipt_registration' => Arr::get($responsePayload, 'receipt_registration'),
                    'payment_id' => Arr::get($responsePayload, 'id'),
                ],
                'error_message' => null,
            ]
        );
    }

    /**
     * @param array<string, mixed> $remotePayload
     */
    private function updateEmbeddedReceiptRecord(OrderPayment $payment, array $remotePayload): void
    {
        $receipt = $payment->receipts()->latest('id')->first();
        if (!$receipt) {
            return;
        }

        $status = Arr::get($remotePayload, 'receipt_registration');
        if (!is_string($status) || $status === '') {
            if ($payment->status === OrderPayment::STATUS_SUCCEEDED) {
                $status = PaymentReceipt::STATUS_SUCCEEDED;
            } elseif ($payment->status === OrderPayment::STATUS_CANCELED) {
                $status = PaymentReceipt::STATUS_CANCELED;
            } else {
                return;
            }
        }

        $responsePayload = $receipt->response_payload ?? [];
        $responsePayload['receipt_registration'] = $status;
        $responsePayload['payment_id'] = $payment->external_payment_id;

        $receipt->status = $status;
        $receipt->response_payload = $responsePayload;
        $receipt->save();
    }

    private function receiptsEnabled(): bool
    {
        return (bool) config('services.yookassa.receipts.enabled', false);
    }

    private function receiptMode(): string
    {
        return (string) config('services.yookassa.receipts.mode', 'embedded');
    }

    private function api(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.yookassa.base_url'), '/'))
            ->withBasicAuth(
                (string) config('services.yookassa.shop_id'),
                (string) config('services.yookassa.secret_key'),
            )
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('services.yookassa.timeout', 15))
            ->retry(3, 200);
    }

    private function formatAmount(string|float|int $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function normalizePhone(?string $phone): string
    {
        if ($phone === null) {
            return '';
        }

        return preg_replace('/\D+/', '', $phone) ?? '';
    }
}
