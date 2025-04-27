<?php

// app/Services/ReportService.php
namespace App\Services;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function getDashboardSummary()
    {
        $today = Carbon::today();

        return [
            'revenue' => [
                'today' => $this->transactionRepository
                    ->getTotalCredits(null, $today, $today),
                'month' => $this->transactionRepository
                    ->getTotalCredits(null, $today->startOfMonth(), $today->endOfMonth()),
                'year' => $this->transactionRepository
                    ->getTotalCredits(null, $today->startOfYear(), $today->endOfYear())
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
        return [
            'income' => $this->transactionRepository->getTotalCredits(null, $startDate, $endDate),
            'expenses' => $this->transactionRepository->getTotalDebits(null, $startDate, $endDate),
            'net_profit' => $this->transactionRepository->getTotalCredits(null, $startDate, $endDate) -
                $this->transactionRepository->getTotalDebits(null, $startDate, $endDate),
            'invoices' => $this->invoiceRepository->getTotalRevenue('custom', $startDate, $endDate)
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
            'transactions' => $this->transactionRepository->getByClient($clientId, $startDate, $endDate),
            'invoices' => $this->invoiceRepository->getByClient($clientId)
        ];
    }
}
