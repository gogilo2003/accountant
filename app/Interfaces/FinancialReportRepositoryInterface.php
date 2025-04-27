<?php

// app/Interfaces/FinancialReportRepositoryInterface.php
namespace App\Interfaces;

interface FinancialReportRepositoryInterface
{
    public function getProfitLossReport($startDate, $endDate);
    public function getBalanceSheet($date);
    public function getCashFlowStatement($startDate, $endDate);
    public function getIncomeByClient($startDate, $endDate);
    public function getExpenseBreakdown($startDate, $endDate);
    public function getAgingReceivables();
    public function getTaxSummary($startDate, $endDate);
}
