<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand">Admin Panel</a>
        <div class="d-flex gap-2">
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm">Site</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <aside class="col-12 col-lg-2 bg-white border-end min-vh-100 p-3">
            <div class="list-group">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                    Dashboard
                </a>
                @if(auth()->user()?->hasRole(\App\Models\Role::ROLE_ADMIN))
                    <a href="{{ route('admin.roles.index') }}" class="list-group-item list-group-item-action">
                        Roles
                    </a>
                @endif
                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                    Users
                </a>
                <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">
                    Products
                </a>
                <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">
                    Orders
                </a>
            </div>
        </aside>
        <main class="col-12 col-lg-10 p-4">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
