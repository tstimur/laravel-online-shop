@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-3">Create role</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.roles._form', [
                'action' => route('admin.roles.store'),
                'method' => 'POST',
                'role' => null,
            ])
        </div>
    </div>
@endsection
