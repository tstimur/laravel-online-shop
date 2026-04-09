@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-3">Edit user</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.users._form', [
                'action' => route('admin.users.update', $user),
                'method' => 'PUT',
                'user' => $user,
                'roles' => $roles,
                'selectedRoleId' => $user->roles->pluck('id')->first(),
                'showPassword' => false,
            ])
        </div>
    </div>
@endsection
