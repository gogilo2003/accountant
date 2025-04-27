<?php

// app/Repositories/InvoiceRepository.php
namespace App\Repositories;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Models\Invoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function all()
    {
        return Invoice::with(['client', 'transactions'])->get();
    }

    public function find($id)
    {
        return Invoice::with(['client', 'transactions'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Invoice::create([
            'client_id' => $data['client_id'],
            'invoice_number' => $data['invoice_number'] ?? $this->getNextInvoiceNumber(),
            'issue_date' => $data['issue_date'] ?? Carbon::now(),
            'due_date' => $data['due_date'] ?? Carbon::now()->addDays(30),
            'amount' => $data['amount'],
            'status' => $data['status'] ?? 'draft',
            'description' => $data['description'] ?? null
        ]);
    }

    public function update($id, array $data)
    {
        $invoice = $this->find($id);
        $invoice->update([
            'issue_date' => $data['issue_date'] ?? $invoice->issue_date,
            'due_date' => $data['due_date'] ?? $invoice->due_date,
            'amount' => $data['amount'] ?? $invoice->amount,
            'status' => $data['status'] ?? $invoice->status,
            'description' => $data['description'] ?? $invoice->description
        ]);
        return $invoice;
    }

    public function delete($id)
    {
        $invoice = $this->find($id);
        return $invoice->delete();
    }

    public function paginate($perPage = 10)
    {
        return Invoice::with(['client'])
            ->orderBy('issue_date', 'desc')
            ->paginate($perPage);
    }

    public function search($query)
    {
        return Invoice::where('invoice_number', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhereHas('client', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->paginate(10);
    }

    public function getByClient($clientId)
    {
        return Invoice::where('client_id', $clientId)
            ->orderBy('issue_date', 'desc')
            ->paginate(10);
    }

    public function getByStatus($status)
    {
        return Invoice::where('status', $status)
            ->orderBy('issue_date', 'desc')
            ->paginate(10);
    }

    public function getDrafts()
    {
        return $this->getByStatus('draft');
    }

    public function getSent()
    {
        return $this->getByStatus('sent');
    }

    public function getPaid()
    {
        return $this->getByStatus('paid');
    }

    public function getOverdue()
    {
        return Invoice::where('status', 'sent')
            ->where('due_date', '<', Carbon::today())
            ->orderBy('due_date')
            ->paginate(10);
    }

    public function getNextInvoiceNumber()
    {
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $lastNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 3) : 0;
        return 'INV' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    public function markAsSent($invoiceId)
    {
        $invoice = $this->find($invoiceId);
        return $invoice->update([
            'status' => 'sent',
            'sent_at' => Carbon::now()
        ]);
    }

    public function markAsPaid($invoiceId, array $paymentData)
    {
        $invoice = $this->find($invoiceId);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => Carbon::now()
        ]);

        // Create payment transaction
        Transaction::create([
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoiceId,
            'type' => 'payment',
            'direction' => 'credit',
            'amount' => $invoice->amount,
            'transaction_date' => Carbon::now(),
            'description' => 'Payment for invoice ' . $invoice->invoice_number,
            'reference' => $paymentData['reference'] ?? null
        ]);

        return $invoice;
    }

    public function getClientInvoicesSummary($clientId)
    {
        return [
            'total' => Invoice::where('client_id', $clientId)->count(),
            'paid' => Invoice::where('client_id', $clientId)
                ->where('status', 'paid')
                ->count(),
            'pending' => Invoice::where('client_id', $clientId)
                ->where('status', 'sent')
                ->count(),
            'overdue' => Invoice::where('client_id', $clientId)
                ->where('status', 'sent')
                ->where('due_date', '<', Carbon::today())
                ->count(),
            'total_amount' => Invoice::where('client_id', $clientId)
                ->where('status', 'paid')
                ->sum('amount'),
            'outstanding' => Invoice::where('client_id', $clientId)
                ->where('status', 'sent')
                ->sum('amount')
        ];
    }

    public function getTotalRevenue($period = 'monthly')
    {
        return Invoice::where('status', 'paid')
            ->when($period === 'yearly', function ($q) {
                return $q->selectRaw('YEAR(paid_at) as year, SUM(amount) as total')
                    ->groupBy('year');
            })
            ->when($period === 'monthly', function ($q) {
                return $q->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, SUM(amount) as total')
                    ->groupBy('year', 'month');
            })
            ->when($period === 'weekly', function ($q) {
                return $q->selectRaw('YEAR(paid_at) as year, WEEK(paid_at) as week, SUM(amount) as total')
                    ->groupBy('year', 'week');
            })
            ->get();
    }

    public function getOutstandingRevenue()
    {
        return Invoice::where('status', 'sent')
            ->sum('amount');
    }

    public function exists($invoiceNumber)
    {
        return Invoice::where('invoice_number', $invoiceNumber)->exists();
    }
}
