<?php

// app/Repositories/TransactionRepository.php
namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function all()
    {
        return Transaction::with(['client', 'invoice'])->get();
    }

    public function find($id)
    {
        return Transaction::with(['client', 'invoice'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Transaction::create([
            'client_id' => $data['client_id'],
            'invoice_id' => $data['invoice_id'] ?? null,
            'type' => $data['type'],
            'direction' => $data['direction'],
            'amount' => $data['amount'],
            'transaction_date' => $data['transaction_date'] ?? Carbon::now(),
            'description' => $data['description'],
            'reference' => $data['reference'] ?? null
        ]);
    }

    public function update($id, array $data)
    {
        $transaction = $this->find($id);
        $transaction->update([
            'type' => $data['type'] ?? $transaction->type,
            'direction' => $data['direction'] ?? $transaction->direction,
            'amount' => $data['amount'] ?? $transaction->amount,
            'transaction_date' => $data['transaction_date'] ?? $transaction->transaction_date,
            'description' => $data['description'] ?? $transaction->description,
            'reference' => $data['reference'] ?? $transaction->reference
        ]);
        return $transaction;
    }

    public function delete($id)
    {
        $transaction = $this->find($id);
        return $transaction->delete();
    }

    public function paginate($perPage = 15)
    {
        return Transaction::with(['client', 'invoice'])
            ->orderBy('transaction_date', 'desc')
            ->paginate($perPage);
    }

    public function search($query)
    {
        return Transaction::where('description', 'like', "%{$query}%")
            ->orWhere('reference', 'like', "%{$query}%")
            ->orWhereHas('client', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->paginate(15);
    }

    public function getByClient($clientId, $startDate = null, $endDate = null, $perPage = 15)
    {
        $query = Transaction::where('client_id', $clientId)
            ->with(['invoice'])
            ->orderBy('transaction_date', 'desc');

        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        return $perPage
            ? $query->paginate($perPage)
            : $query->get();
    }

    public function getByInvoice($invoiceId)
    {
        return Transaction::where('invoice_id', $invoiceId)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function getRecent($limit = 5)
    {
        return Transaction::with(['client', 'invoice'])
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getSummary($period = 'monthly')
    {
        $query = Transaction::select(
            DB::raw('SUM(CASE WHEN direction = "credit" THEN amount ELSE 0 END) as credits'),
            DB::raw('SUM(CASE WHEN direction = "debit" THEN amount ELSE 0 END) as debits'),
            DB::raw('COUNT(*) as count')
        );

        switch ($period) {
            case 'daily':
                $query->groupBy(DB::raw('DATE(transaction_date)'));
                break;
            case 'weekly':
                $query->groupBy(DB::raw('YEAR(transaction_date), WEEK(transaction_date)'));
                break;
            case 'yearly':
                $query->groupBy(DB::raw('YEAR(transaction_date)'));
                break;
            default: // monthly
                $query->groupBy(DB::raw('YEAR(transaction_date), MONTH(transaction_date)'));
        }

        return $query->get();
    }

    public function getClientBalance($clientId)
    {
        $credits = Transaction::where('client_id', $clientId)
            ->where('direction', 'credit')
            ->sum('amount');

        $debits = Transaction::where('client_id', $clientId)
            ->where('direction', 'debit')
            ->sum('amount');

        return $credits - $debits;
    }
    public function getTotalCredits($clientId = null, $startDate = null, $endDate = null)
    {
        $query = Transaction::where('direction', 'credit');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        return $query->sum('amount');
    }

    public function getTotalDebits($clientId = null, $startDate = null, $endDate = null)
    {
        $query = Transaction::where('direction', 'debit');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        return $query->sum('amount');
    }

    public function getPeriodicSummary($startDate, $endDate)
    {
        return Transaction::select(
            DB::raw('SUM(CASE WHEN direction = "credit" THEN amount ELSE 0 END) as credits'),
            DB::raw('SUM(CASE WHEN direction = "debit" THEN amount ELSE 0 END) as debits'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->first();
    }

    public function getCountBetweenDates(Carbon $startDate, Carbon $endDate): int
    {
        return Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->count();
    }
}
