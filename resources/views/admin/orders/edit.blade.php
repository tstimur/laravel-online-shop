@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-3">Edit order</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.orders._form', [
                'action' => route('admin.orders.update', $order),
                'method' => 'PUT',
                'order' => $order,
                'users' => $users,
                'products' => $products,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->all(),
            ])
        </div>
    </div>
@endsection
