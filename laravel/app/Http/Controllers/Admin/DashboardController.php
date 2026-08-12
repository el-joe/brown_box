<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'todayOrders' => $this->reportService->todayOrdersCount(),
            'todayRevenue' => $this->reportService->todayRevenue(),
            'pendingPayments' => $this->reportService->pendingPaymentsCount(),
            'lowStockCount' => $this->reportService->lowStockCount(),
            'recentOrders' => $this->reportService->recentOrders(),
            'monthlyRevenue' => $this->reportService->monthlyRevenue(),
        ]);
    }
}
