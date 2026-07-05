@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Order #{{ $order->id }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-outline-primary btn-sm">
                Edit
            </a>
            <form method="POST"
                  action="{{ route('admin.orders.destroy', $order) }}"
                  onsubmit="return confirm('Delete order?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><strong>User:</strong> {{ $order->user?->full_name ?? '-' }}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $order->user?->email ?? '-' }}</div>
                    <div class="mb-2"><strong>Status:</strong> {{ $order->status }}</div>
                    <div class="mb-2"><strong>Payment:</strong> {{ $order->payment_method_label }}</div>
                    <div class="mb-2"><strong>Shipping address:</strong> {{ $order->shipping_address ?? '-' }}</div>
                    <div class="mb-2"><strong>Total:</strong> {{ number_format($order->total, 2, '.', ' ') }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Items</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($order->items as $item)
                                <tr>
                                    <td>{{ $item->product?->name ?? '-' }}</td>
                                    <td>{{ number_format($item->price, 2, '.', ' ') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price * $item->quantity, 2, '.', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No items.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
