@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="container-fluid px-2 px-sm-3 py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <h2 class="text-center mb-3 mb-md-4 h3 h2-md">Профиль пользователя</h2>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(!$user->hasVerifiedEmail())
                            <div class="alert alert-warning">
                                <div class="fw-semibold mb-1">Email не подтверждён</div>
                                <div class="small mb-2">
                                    Подтвердите почту, перейдя по ссылке из письма. Без подтверждения некоторые функции могут быть недоступны.
                                </div>
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        Отправить ссылку ещё раз
                                    </button>
                                </form>
                            </div>
                        @endif

                        <form id="profile-form" method="POST" action="{{ route('profile.update', $user) }}">
                            @csrf
                            @method('PATCH')

                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="first_name" class="form-label small">Имя</label>
                                        <input type="text"
                                               name="first_name"
                                               id="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror"
                                               value="{{ old('first_name', $user->first_name) }}">
                                        @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="last_name" class="form-label small">Фамилия</label>
                                        <input type="text"
                                               name="last_name"
                                               id="last_name"
                                               class="form-control @error('last_name') is-invalid @enderror"
                                               value="{{ old('last_name', $user->last_name) }}">
                                        @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2 mb-md-3">
                                <label for="email" class="form-label small">Email</label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 mb-md-4">
                                <label for="phone" class="form-label small">Телефон</label>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="+7 (___) ___-__-__">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2">
                                    Сохранить изменения
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="d-grid">
                            <a href="{{ route('password.form') }}" class="btn btn-outline-secondary py-2">
                                Изменить пароль
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <h3 class="h5 mb-3">Адреса доставки</h3>

                        <form method="POST" action="{{ route('addresses.store') }}" class="mb-4">
                            @csrf
                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <label for="address_city" class="form-label small">Город</label>
                                    <input type="text"
                                           name="city"
                                           id="address_city"
                                           class="form-control @error('city', 'addressStore') is-invalid @enderror"
                                           value="{{ old('city') }}">
                                    @error('city', 'addressStore')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="address_street" class="form-label small">Улица</label>
                                    <input type="text"
                                           name="street"
                                           id="address_street"
                                           class="form-control @error('street', 'addressStore') is-invalid @enderror"
                                           value="{{ old('street') }}">
                                    @error('street', 'addressStore')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="address_house" class="form-label small">Дом</label>
                                    <input type="text"
                                           name="house"
                                           id="address_house"
                                           class="form-control @error('house', 'addressStore') is-invalid @enderror"
                                           value="{{ old('house') }}">
                                    @error('house', 'addressStore')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="address_apartment" class="form-label small">Квартира</label>
                                    <input type="text"
                                           name="apartment"
                                           id="address_apartment"
                                           class="form-control @error('apartment', 'addressStore') is-invalid @enderror"
                                           value="{{ old('apartment') }}">
                                    @error('apartment', 'addressStore')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="address_label" class="form-label small">Метка</label>
                                    <input type="text"
                                           name="label"
                                           id="address_label"
                                           class="form-control @error('label', 'addressStore') is-invalid @enderror"
                                           value="{{ old('label') }}"
                                           placeholder="Дом, офис, дача">
                                    @error('label', 'addressStore')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary py-2">
                                    Добавить адрес
                                </button>
                            </div>
                        </form>

                        @if($addresses->isEmpty())
                            <div class="alert alert-info mb-0">
                                Адресов пока нет.
                            </div>
                        @else
                            @foreach($addresses as $address)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $address->label ?: 'Без метки' }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $address->full_address ?: 'Адрес не заполнен' }}
                                            </div>
                                        </div>
                                        @if($address->is_default)
                                            <span class="badge text-bg-success">Основной</span>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('addresses.update', $address) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="row g-2 g-md-3">
                                            <div class="col-12 col-md-6">
                                                <label for="city-{{ $address->id }}" class="form-label small">Город</label>
                                                <input type="text"
                                                       name="city"
                                                       id="city-{{ $address->id }}"
                                                       class="form-control @error('city', 'addressUpdate') is-invalid @enderror"
                                                       value="{{ $address->city }}">
                                                @error('city', 'addressUpdate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="street-{{ $address->id }}" class="form-label small">Улица</label>
                                                <input type="text"
                                                       name="street"
                                                       id="street-{{ $address->id }}"
                                                       class="form-control @error('street', 'addressUpdate') is-invalid @enderror"
                                                       value="{{ $address->street }}">
                                                @error('street', 'addressUpdate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label for="house-{{ $address->id }}" class="form-label small">Дом</label>
                                                <input type="text"
                                                       name="house"
                                                       id="house-{{ $address->id }}"
                                                       class="form-control @error('house', 'addressUpdate') is-invalid @enderror"
                                                       value="{{ $address->house }}">
                                                @error('house', 'addressUpdate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label for="apartment-{{ $address->id }}" class="form-label small">Квартира</label>
                                                <input type="text"
                                                       name="apartment"
                                                       id="apartment-{{ $address->id }}"
                                                       class="form-control @error('apartment', 'addressUpdate') is-invalid @enderror"
                                                       value="{{ $address->apartment }}">
                                                @error('apartment', 'addressUpdate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label for="label-{{ $address->id }}" class="form-label small">Метка</label>
                                                <input type="text"
                                                       name="label"
                                                       id="label-{{ $address->id }}"
                                                       class="form-control @error('label', 'addressUpdate') is-invalid @enderror"
                                                       value="{{ $address->label }}">
                                                @error('label', 'addressUpdate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-grid mt-3">
                                            <button type="submit" class="btn btn-outline-primary py-2">
                                                Сохранить изменения
                                            </button>
                                        </div>
                                    </form>

                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if(!$address->is_default)
                                            <form method="POST" action="{{ route('addresses.default', $address) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    Сделать основным
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Удалить адрес?')">
                                                Удалить
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const profileForm = document.getElementById('profile-form');
        if (profileForm) {
            profileForm.addEventListener('submit', function () {
                const phoneInput = document.querySelector('input[name="phone"]');
                if (phoneInput.value) {
                    phoneInput.value = phoneInput.value.replace(/\+/g, '');
                }
            });
        }
    </script>
@endpush
