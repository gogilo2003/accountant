<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(Request $request)
    {
        return Inertia::render('Transactions/Index', [
            'transactions' => $this->transactionService->getAllTransactions($request->all()),
            'filters' => $request->only(['search'])
        ]);
    }

    public function recordPayment(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'reference' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $transaction = $this->transactionService->recordPayment($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Payment recorded successfully');
    }

    public function show($id)
    {
        return Inertia::render('Transactions/Show', [
            'transaction' => $this->transactionService->getTransactionById($id)
        ]);
    }
}
