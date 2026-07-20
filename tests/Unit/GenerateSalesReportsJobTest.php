<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\GenerateSalesReportsJob;
use App\Service\SalesReportService;
use Mockery;
use Tests\TestCase;

class GenerateSalesReportsJobTest extends TestCase
{
    public function test_refreshes_recent_sales_reports(): void
    {
        $salesReportService = Mockery::mock(SalesReportService::class);
        $salesReportService
            ->shouldReceive('refreshRecentReports')
            ->once();

        (new GenerateSalesReportsJob())->handle($salesReportService);
    }
}
