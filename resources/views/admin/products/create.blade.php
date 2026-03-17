@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-3">Create product</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.products._form', [
                'action' => route('admin.products.store'),
                'method' => 'POST',
                'product' => null,
                'categories' => $categories,
            ])
        </div>
    </div>
@endsection
