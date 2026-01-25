@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">Корзина</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div id="cart-content">
            @include('cart._content', ['items' => $items, 'totalQuantity' => $totalQuantity, 'totalPrice' => $totalPrice])
        </div>

        @if($totalQuantity > 0)
            <div class="mt-4" data-order-block>
                @auth
                    @if($defaultAddress)
                        <div id="order-form" class="card mb-3">
                            <div class="card-body">
                                <h2 class="h5 mb-3">Адрес доставки</h2>
                                <div class="mb-2">
                                    {{ $defaultAddress->full_address }}
                                </div>
                                @if($defaultAddress->label)
                                    <div class="text-muted small mb-3">
                                        Метка: {{ $defaultAddress->label }}
                                    </div>
                                @endif
                                <a href="{{ route('profile.form') }}" class="btn btn-outline-secondary btn-sm">
                                    Изменить адрес
                                </a>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('orders.store') }}">
                            @csrf
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h6 mb-2">Способ оплаты</h3>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="payment_method"
                                               id="payment-cash"
                                               value="cash"
                                            @checked(old('payment_method', 'cash') === 'cash')>
                                        <label class="form-check-label" for="payment-cash">
                                            Наличными при получении
                                        </label>
                                    </div>
                                    <div class="form-check mt-1">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="payment_method"
                                               id="payment-card"
                                               value="card"
                                            @checked(old('payment_method') === 'card')>
                                        <label class="form-check-label" for="payment-card">
                                            Картой при получении
                                        </label>
                                    </div>
                                    @error('payment_method')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Оформить заказ</button>
                        </form>
                    @else
                        <div class="alert alert-warning mb-0">
                            Добавьте адрес в профиле, чтобы оформить заказ.
                            <a href="{{ route('profile.form') }}">Перейти в профиль</a>.
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning mb-0">
                        Для оформления заказа необходимо <a href="{{ route('login.form') }}">войти</a>.
                    </div>
                @endauth
            </div>
        @endif
    </div>
@endsection
