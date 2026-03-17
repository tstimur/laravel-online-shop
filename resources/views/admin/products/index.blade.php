@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Products</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">Create product</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <div class="text-muted small">SKU: {{ $product->sku }}</div>
                            </td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td>{{ number_format($product->price, 2, '.', ' ') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                @if($product->status === \App\Models\Product::STATUS_ACTIVE)
                                    <span class="badge text-bg-success">active</span>
                                @else
                                    <span class="badge text-bg-secondary">inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.products.show', $product) }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    View
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.products.destroy', $product) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No products yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
