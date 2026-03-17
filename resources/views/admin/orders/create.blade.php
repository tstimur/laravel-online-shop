@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-3">Create order</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.orders._form', [
                'action' => route('admin.orders.store'),
                'method' => 'POST',
                'order' => null,
                'users' => $users,
                'products' => $products,
                'items' => [['product_id' => '', 'quantity' => 1, 'price' => 0]],
            ])
        </div>
    </div>
@endsection
