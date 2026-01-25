<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel Shop') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-light mb-4">
    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand">Laravel Shop</a>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-primary btn-sm">Каталог</a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">Все товары</a>
            @php($cartCount = collect(session('cart.items', []))->sum())
            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm">
                Корзина
                <span class="badge text-bg-secondary ms-1" data-cart-count>{{ $cartCount }}</span>
            </a>

            @guest
                <a href="{{ route('login.form') }}" class="btn btn-primary btn-sm">Вход</a>
                <a href="{{ route('register.form') }}" class="btn btn-outline-secondary btn-sm">Регистрация</a>
            @endguest

            @auth
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">Мои заказы</a>
                <a href="{{ route('profile.form') }}" class="btn btn-outline-secondary btn-sm">Профиль</a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Выход</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<main class="container">
    @yield('content')  {{-- сюда вставляется контент каждой страницы --}}
</main>

<script>
    (function () {
        function csrfToken() {
            const el = document.querySelector('meta[name=\"csrf-token\"]');
            return el ? el.getAttribute('content') : '';
        }

        function setCartCount(count) {
            const badge = document.querySelector('[data-cart-count]');
            if (!badge) return;
            badge.textContent = String(count ?? 0);
            toggleOrderBlock(count);
        }

        function toggleOrderBlock(count) {
            const block = document.querySelector('[data-order-block]');
            if (!block) return;
            const hasItems = Number(count ?? 0) > 0;
            block.classList.toggle('d-none', !hasItems);
        }

        async function submitCartForm(form) {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            if (typeof data.cartCount !== 'undefined') {
                setCartCount(data.cartCount);
            }

            const cartContent = document.getElementById('cart-content');
            if (cartContent && typeof data.html === 'string') {
                cartContent.innerHTML = data.html;
            }
        }

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (!form.hasAttribute('data-ajax-cart')) return;
            e.preventDefault();
            submitCartForm(form);
        });

        document.addEventListener('change', function (e) {
            const input = e.target;
            if (!(input instanceof HTMLInputElement)) return;
            const form = input.closest('form[data-ajax-cart]');
            if (!form) return;
            if (form.getAttribute('data-cart-action') !== 'set') return;
            submitCartForm(form);
        });
    })();
</script>
</body>
</html>
