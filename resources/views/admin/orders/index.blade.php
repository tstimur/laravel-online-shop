@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Orders</h1>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm">Create order</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user?->full_name ?? '-' }}</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->items_count }}</td>
                            <td>{{ number_format($order->total, 2, '.', ' ') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    View
                                </a>
                                <a href="{{ route('admin.orders.edit', $order) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.orders.destroy', $order) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete order?');">
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
                            <td colspan="6" class="text-center text-muted py-4">No orders yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
