<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\DailySalesReport;
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
        $savedReports = DailySalesReport::query()
            ->whereBetween('report_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(fn (DailySalesReport $report): string => $report->report_date->toDateString());

        $reportDays = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->toDateString();
            $dailyReport = $savedReports->get($dateKey);

            $reportDays[] = [
                'date' => $date->copy(),
                'orders_count' => (int) ($dailyReport?->successful_orders ?? 0),
                'revenue' => (string) ($dailyReport?->revenue ?? '0'),
            ];
        }

        return [
            'total_orders' => $savedReports->sum('total_orders'),
            'successful_orders' => $savedReports->sum('successful_orders'),
            'revenue' => number_format((float) $savedReports->sum('revenue'), 2, '.', ''),
            'canceled_orders' => $savedReports->sum('canceled_orders'),
            'daily_sales' => $reportDays,
        ];
    }

    public function refreshRecentReports(): void
    {
        $startDate = now()->startOfDay()->subDays(6);

        for ($date = $startDate; $date->lte(now()); $date->addDay()) {
            $reportDate = $date->copy();
            $orders = Order::query()
                ->whereBetween('created_at', [$reportDate->copy()->startOfDay(), $reportDate->copy()->endOfDay()]);

            DailySalesReport::query()->updateOrCreate(
                ['report_date' => $reportDate->toDateString()],
                [
                    'total_orders' => (clone $orders)->count(),
                    'successful_orders' => (clone $orders)->whereIn('status', $this->successfulStatuses())->count(),
                    'revenue' => (clone $orders)->whereIn('status', $this->successfulStatuses())->sum('total'),
                    'canceled_orders' => (clone $orders)->where('status', Order::STATUS_CANCELED)->count(),
                    'generated_at' => now(),
                ],
            );
        }
    }

    /**
     * @return array<int, string>
     */
    private function successfulStatuses(): array
    {
        return [
            Order::STATUS_PAID,
            Order::STATUS_SHIPPED,
            Order::STATUS_COMPLETED,
        ];
    }
}
