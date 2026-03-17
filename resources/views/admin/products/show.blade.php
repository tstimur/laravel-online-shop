@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $product->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                Edit
            </a>
            <form method="POST"
                  action="{{ route('admin.products.destroy', $product) }}"
                  onsubmit="return confirm('Delete product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                     class="img-fluid rounded border"
                     alt="{{ $product->name }}">
            @else
                <div class="border rounded d-flex align-items-center justify-content-center bg-light"
                     style="height: 240px;">
                    <span class="text-muted">No image</span>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><strong>SKU:</strong> {{ $product->sku }}</div>
                    <div class="mb-2"><strong>Category:</strong> {{ $product->category?->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Price:</strong> {{ number_format($product->price, 2, '.', ' ') }}</div>
                    <div class="mb-2"><strong>Stock:</strong> {{ $product->stock }}</div>
                    <div class="mb-2"><strong>Status:</strong> {{ $product->status }}</div>
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <div class="text-muted">{{ $product->description ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
