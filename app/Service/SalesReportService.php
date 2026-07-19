<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Order;
use Illuminate\Support\Carbon;

class SalesReportService
{
    /**
     * @return array{
     *     total_orders: int,
     *     successful_orders: int,
     *     revenue: string,
     *     canceled_orders: int,
     *     daily_sales: array<int, array{date: Carbon, orders_count: int, revenue: string}>
     * }
     */
    public function getDashboardReport(): array
    {
        $startDate = now()->startOfDay()->subDays(6);
        $endDate = now()->endOfDay();
        $successfulStatuses = [
            Order::STATUS_PAID,
            Order::STATUS_SHIPPED,
            Order::STATUS_COMPLETED,
        ];

        $orders = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        $dailySales = (clone $orders)
            ->whereIn('status', $successfulStatuses)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, COALESCE(SUM(total), 0) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $reportDays = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->toDateString();
            $dailyReport = $dailySales->get($dateKey);

            $reportDays[] = [
                'date' => $date->copy(),
                'orders_count' => (int) ($dailyReport?->orders_count ?? 0),
                'revenue' => (string) ($dailyReport?->revenue ?? '0'),
            ];
        }

        return [
            'total_orders' => (clone $orders)->count(),
            'successful_orders' => (clone $orders)->whereIn('status', $successfulStatuses)->count(),
            'revenue' => (string) (clone $orders)->whereIn('status', $successfulStatuses)->sum('total'),
            'canceled_orders' => (clone $orders)->where('status', Order::STATUS_CANCELED)->count(),
            'daily_sales' => $reportDays,
        ];
    }
}
