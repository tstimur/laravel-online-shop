@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $user->full_name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                Edit
            </a>
            <form method="POST"
                  action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Delete user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->full_name }}"
                             class="img-fluid rounded mb-3">
                    @endif
                    <div class="mb-2"><strong>Email:</strong> {{ $user->email }}</div>
                    <div class="mb-2"><strong>Phone:</strong> {{ $user->phone ?? '-' }}</div>
                    <div class="mb-2"><strong>Status:</strong> {{ $user->status }}</div>
                    <div class="mb-2">
                        <strong>Role:</strong> {{ $user->roles->first()?->name ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Reset password</h2>
                    <form method="POST" action="{{ route('admin.users.password', $user) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="password" class="form-label">New password</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="form-control"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-outline-danger">Reset password</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">User orders</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
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
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->items_count }}</td>
                                    <td>{{ number_format($order->total, 2, '.', ' ') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No orders yet.</td>
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
        </div>
    </div>
@endsection
