<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Service\SalesReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSalesReportsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    public function __construct()
    {
        $this->onQueue('orders.reports.sales');
    }

    public function handle(SalesReportService $salesReportService): void
    {
        $salesReportService->refreshRecentReports();
    }
}
