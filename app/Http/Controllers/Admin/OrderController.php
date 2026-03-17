<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\AdminOrderDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStoreRequest;
use App\Http\Requests\Admin\OrderUpdateRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Service\AdminOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $users = User::query()->orderBy('last_name')->get();
        $products = Product::query()->orderBy('name')->get();

        return view('admin.orders.create', compact('users', 'products'));
    }

    public function store(OrderStoreRequest $request, AdminOrderService $service): RedirectResponse
    {
        $order = $service->create(AdminOrderDto::fromRequest($request));

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order created.');
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $order->load('items');

        $users = User::query()->orderBy('last_name')->get();
        $products = Product::query()->orderBy('name')->get();

        return view('admin.orders.edit', compact('order', 'users', 'products'));
    }

    public function update(
        OrderUpdateRequest $request,
        Order $order,
        AdminOrderService $service
    ): RedirectResponse {
        $service->update($order, AdminOrderDto::fromRequest($request));

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order updated.');
    }

    public function destroy(Order $order, AdminOrderService $service): RedirectResponse
    {
        $service->delete($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order deleted.');
    }
}
