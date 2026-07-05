<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Jobs\SendOrderCreatedNotificationJob;
use App\Models\Order;
use App\Service\OrderService;
use App\Service\SessionCartService;
use App\Service\YooKassaPaymentService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

class OrderController extends Controller
{
    public function index(): Factory|View
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with(['items.product', 'latestPayment.latestReceipt'])
            ->orderByDesc('created_at')
            ->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function store(
        OrderStoreRequest $request,
        OrderService $service,
        SessionCartService $cart,
        YooKassaPaymentService $paymentService,
    ): RedirectResponse {
        $user = Auth::user();

        $order = $service->createOrder(
            $user,
            $request->validated()['payment_method'],
            $cart
        );

        SendOrderCreatedNotificationJob::dispatch($order->id);

        if ($order->payment_method === Order::PAYMENT_METHOD_YOOKASSA) {
            try {
                $payment = $paymentService->createPaymentForOrder($order);

                return redirect()->away($payment->confirmation_url ?? route('orders.index'));
            } catch (Throwable) {
                return redirect()
                    ->route('orders.index')
                    ->with('error', 'Заказ создан, но ссылку на оплату получить не удалось. Попробуйте снова из списка заказов.');
            }
        }

        return redirect()
            ->route('orders.index')
            ->with('success', 'Заказ создан.');
    }

    public function pay(Order $order, YooKassaPaymentService $paymentService): RedirectResponse
    {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->whereKey($order->id)
            ->firstOrFail();

        try {
            $payment = $paymentService->createPaymentForOrder($order);

            return redirect()->away($payment->confirmation_url ?? route('orders.index'));
        } catch (Throwable) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Не удалось создать новую ссылку на оплату. Попробуйте позже.');
        }
    }

    public function updateStatus(
        Order $order,
        OrderStatusRequest $request,
        OrderService $service
    ): RedirectResponse {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->whereKey($order->id)
            ->firstOrFail();

        $status = $request->validated()['status'];

        if ($status === Order::STATUS_PAID) {
            $service->markAsPaid($order);
            $message = 'Заказ оплачен.';
        } else {
            $service->cancel($order);
            $message = 'Заказ отменен.';
        }

        return redirect()
            ->route('orders.index')
            ->with('success', $message);
    }
}
