<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service\SalesReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(SalesReportService $salesReportService): View
    {
        return view('admin.dashboard', [
            'report' => $salesReportService->getDashboardReport(),
        ]);
    }
}
