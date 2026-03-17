@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Roles</h1>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">Create role</a>
    </div>

    @php($systemRoles = [
        \App\Models\Role::ROLE_USER,
        \App\Models\Role::ROLE_ADMIN,
        \App\Models\Role::ROLE_MANAGER,
    ])

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->slug }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Edit
                                </a>
                                @if(!in_array($role->slug, $systemRoles, true))
                                    <form method="POST"
                                          action="{{ route('admin.roles.destroy', $role) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="badge text-bg-secondary">System</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No roles yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
