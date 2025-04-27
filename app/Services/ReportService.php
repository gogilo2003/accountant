<?php

// app/Services/ReportService.php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;

class ReportService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private TransactionRepositoryInterface $transactionRepository,
        private ClientRepositoryInterface $clientRepository
    ) {}

    public function getDashboardSummary()
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $yearStart = $today->copy()->startOfYear();

        // Get today's transactions summary
        $todaySummary = $this->transactionRepository->getPeriodicSummary($today, $today);
        $monthSummary = $this->transactionRepository->getPeriodicSummary($monthStart, $today);
        $yearSummary = $this->transactionRepository->getPeriodicSummary($yearStart, $today);

        return [
            'revenue' => [
                'today' => $todaySummary->credits,
                'month' => $monthSummary->credits,
                'year' => $yearSummary->credits
            ],
            'expenses' => [
                'today' => $todaySummary->debits,
                'month' => $monthSummary->debits,
                'year' => $yearSummary->debits
            ],
            'outstanding' => $this->invoiceRepository->getOutstandingRevenue(),
            'invoices' => [
                'draft' => $this->invoiceRepository->getDrafts()->total(),
                'sent' => $this->invoiceRepository->getSent()->total(),
                'overdue' => $this->invoiceRepository->getOverdue()->total()
            ],
            'recent_transactions' => $this->transactionRepository->getRecent(5)
        ];
    }

    public function getProfitLossReport($startDate, $endDate)
    {
        $summary = $this->transactionRepository->getPeriodicSummary($startDate, $endDate);

        return [
            'income' => $summary->credits,
            'expenses' => $summary->debits,
            'net_profit' => $summary->credits - $summary->debits,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ]
        ];
    }

    public function getAgingReport()
    {
        $ranges = [
            '0-30' => [now(), now()->subDays(30)],
            '31-60' => [now()->subDays(31), now()->subDays(60)],
            '61-90' => [now()->subDays(61), now()->subDays(90)],
            '90+' => [now()->subDays(91), null]
        ];

        $results = [];
        foreach ($ranges as $key => $range) {
            $results[$key] = $this->invoiceRepository
                ->getByStatus('sent')
                ->whereBetween('due_date', $range)
                ->sum('amount');
        }

        return $results;
    }

    public function getClientStatement($clientId, $startDate = null, $endDate = null)
    {
        return [
            'client' => $this->clientRepository->find($clientId),
            'balance' => $this->transactionRepository->getClientBalance($clientId),
            'transactions' => $this->transactionRepository->getByClient(
                $clientId,
                $startDate,
                $endDate,
                null // No pagination for statements
            ),
            'invoices' => $this->invoiceRepository->getByClient($clientId),
            'period' => [
                'start' => $startDate ? $startDate->format('Y-m-d') : 'All time',
                'end' => $endDate ? $endDate->format('Y-m-d') : 'Present'
            ]
        ];
    }
    public function getCurrentMonthRevenue(): float
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return $this->transactionRepository->getTotalCredits(null, $start, $end);
    }

    public function getOutstandingRevenue(): float
    {
        return $this->invoiceRepository->getOutstandingRevenue();
    }

    public function getActiveClientCount(): int
    {
        return $this->clientRepository->countActiveClients();
    }

    public function getThisMonthTransactionCount(): int
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return $this->transactionRepository->getCountBetweenDates($start, $end);
    }


    public function getRecentTransactions(int $limit = 5): array
    {
        return $this->transactionRepository->getRecent($limit)
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'description' => $transaction->description,
                    'amount' => $transaction->amount,
                    'direction' => $transaction->direction,
                    'transaction_date' => $transaction->transaction_date,
                    'client' => $transaction->client?->only(['id', 'name']),
                    'invoice' => $transaction->invoice?->only(['id', 'invoice_number'])
                ];
            })
            ->toArray();
    }

    public function getRecentInvoices(int $limit = 5): array
    {
        return $this->invoiceRepository->getRecent($limit)
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date,
                    'client' => $invoice->client?->only(['id', 'name'])
                ];
            })
            ->toArray();
    }

    public function getRevenueChartData(int $months = 6): array
    {
        return Cache::remember("revenue_chart_{$months}_months", now()->addHours(1), function () use ($months) {
            $data = [];
            $now = now();

            for ($i = $months - 1; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i);
                $monthName = $date->format('M Y');

                $startDate = $date->startOfMonth();
                $endDate = $date->endOfMonth();

                $revenue = $this->transactionRepository->getTotalCredits(
                    clientId: null,
                    startDate: $startDate,
                    endDate: $endDate
                );

                $expenses = $this->transactionRepository->getTotalDebits(
                    clientId: null,
                    startDate: $startDate,
                    endDate: $endDate
                );

                $data[] = [
                    'month' => $monthName,
                    'revenue' => $revenue,
                    'expenses' => $expenses,
                    'profit' => $revenue - $expenses
                ];
            }

            return $data;
        });
    }
}
