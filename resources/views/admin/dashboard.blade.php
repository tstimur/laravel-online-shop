@extends('admin.layout')

@section('content')
    <h1 class="mb-4">Dashboard</h1>

    <p class="text-muted">Продажи за последние 7 дней</p>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted">Всего заказов</div>
                    <div class="fs-3 fw-semibold">{{ $report['total_orders'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted">Успешные продажи</div>
                    <div class="fs-3 fw-semibold">{{ $report['successful_orders'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted">Выручка</div>
                    <div class="fs-3 fw-semibold">{{ number_format((float) $report['revenue'], 2, ',', ' ') }} ₽</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted">Отменённые заказы</div>
                    <div class="fs-3 fw-semibold">{{ $report['canceled_orders'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Продажи по дням</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Успешные продажи</th>
                    <th>Выручка</th>
                </tr>
                </thead>
                <tbody>
                @foreach($report['daily_sales'] as $day)
                    <tr>
                        <td>{{ $day['date']->format('d.m.Y') }}</td>
                        <td>{{ $day['orders_count'] }}</td>
                        <td>{{ number_format((float) $day['revenue'], 2, ',', ' ') }} ₽</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
