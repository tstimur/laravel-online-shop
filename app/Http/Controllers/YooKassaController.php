<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Service\YooKassaPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class YooKassaController extends Controller
{
    public function return(Order $order, YooKassaPaymentService $paymentService): RedirectResponse
    {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->with('latestPayment')
            ->whereKey($order->id)
            ->firstOrFail();

        $payment = $order->latestPayment;
        if (!$payment instanceof OrderPayment || $payment->external_payment_id === null) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Для этого заказа не найдена активная онлайн-оплата.');
        }

        try {
            $payment = $paymentService->synchronizePayment($payment);
        } catch (Throwable) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Не удалось обновить статус платежа. Проверьте заказ чуть позже.');
        }

        $order->refresh();

        if ($order->status === Order::STATUS_PAID) {
            return redirect()
                ->route('orders.index')
                ->with('success', 'Оплата подтверждена.');
        }

        if ($payment->status === OrderPayment::STATUS_CANCELED) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Платеж был отменен. Можно сформировать новую ссылку на оплату.');
        }

        return redirect()
            ->route('orders.index')
            ->with('success', 'Мы получили возврат с платёжной страницы. Статус заказа обновится автоматически после подтверждения оплаты.');
    }

    public function webhook(Request $request, YooKassaPaymentService $paymentService): JsonResponse
    {
        $payload = $request->json()->all();
        $paymentService->handleWebhook(is_array($payload) ? $payload : []);

        return response()->json(['ok' => true]);
    }
}
