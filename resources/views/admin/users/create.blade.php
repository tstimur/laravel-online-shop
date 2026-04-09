@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-3">Create user</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.users._form', [
                'action' => route('admin.users.store'),
                'method' => 'POST',
                'user' => null,
                'roles' => $roles,
                'selectedRoleId' => $defaultRoleId,
                'showPassword' => true,
            ])
        </div>
    </div>
@endsection
