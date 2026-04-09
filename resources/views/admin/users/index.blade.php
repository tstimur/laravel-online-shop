@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Create user</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->full_name }}</div>
                                <div class="text-muted small">{{ $user->phone ?? '-' }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->status === \App\Models\User::STATUS_ACTIVE)
                                    <span class="badge text-bg-success">active</span>
                                @else
                                    <span class="badge text-bg-secondary">blocked</span>
                                @endif
                            </td>
                            <td>{{ $user->roles->first()?->name ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    View
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete user?');">
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
                            <td colspan="5" class="text-center text-muted py-4">No users yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
