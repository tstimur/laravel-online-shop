@extends('layouts.app')

@section('title', 'Подтвердите email')

@section('content')
    <div class="container-fluid px-2 px-sm-3 py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <h2 class="text-center mb-3 mb-md-4 h3 h2-md">Подтвердите email</h2>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="alert alert-warning mb-3">
                            Мы отправили ссылку для подтверждения на вашу почту. Перейдите по ссылке из письма, чтобы активировать аккаунт.
                        </div>

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2">
                                    Отправить ссылку ещё раз
                                </button>
                            </div>
                        </form>

                        <div class="text-muted small mt-3">
                            Если письма нет — проверьте “Спам” и убедитесь, что в профиле указан корректный адрес.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
