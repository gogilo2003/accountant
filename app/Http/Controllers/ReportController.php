<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function dashboard()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'revenue' => $this->reportService->getCurrentMonthRevenue(),
                'outstanding' => $this->reportService->getOutstandingRevenue(),
                'clients' => $this->reportService->getActiveClientCount(),
                'transactions' => $this->reportService->getThisMonthTransactionCount()
            ],
            'recentTransactions' => $this->reportService->getRecentTransactions(5),
            'recentInvoices' => $this->reportService->getRecentInvoices(5),
            'chartData' => $this->reportService->getRevenueChartData()
        ]);
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        return Inertia::render('Reports/ProfitLoss', [
            'report' => $this->reportService->getProfitLossReport(
                Carbon::parse($startDate),
                Carbon::parse($endDate)
            ),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    public function clientStatement($clientId, Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Inertia::render('Reports/ClientStatement', [
            'statement' => $this->reportService->getClientStatement(
                $clientId,
                $startDate ? Carbon::parse($startDate) : null,
                $endDate ? Carbon::parse($endDate) : null
            ),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }
}
