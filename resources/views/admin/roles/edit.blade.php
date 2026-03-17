@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-3">Edit role</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.roles._form', [
                'action' => route('admin.roles.update', $role),
                'method' => 'PUT',
                'role' => $role,
            ])
        </div>
    </div>
@endsection
