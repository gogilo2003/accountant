<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function index(Request $request)
    {
        return Inertia::render('invoices/Index', [
            'invoices' => $this->invoiceService->getAllInvoices($request->all(), ['client', 'transactions']),
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function create()
    {
        return Inertia::render('invoices/invoice', [
            'nextNumber' => $this->invoiceService->generateInvoiceNumber()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $invoice = $this->invoiceService->createInvoice($validated);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully');
    }

    public function show($id)
    {
        return Inertia::render('invoices/Show', [
            'invoice' => $this->invoiceService->getInvoiceById($id, ['client']),
            'transactions' => $this->invoiceService->getInvoiceTransactions($id)
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('invoices/Invoice', [
            'invoice' => $this->invoiceService->getInvoiceById($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $this->invoiceService->updateInvoice($id, $validated);

        return redirect()->route('invoices.show', $id)
            ->with('success', 'Invoice updated successfully');
    }

    public function markAsSent($id)
    {
        $this->invoiceService->markAsSent($id);
        return redirect()->back()->with('success', 'Invoice marked as sent');
    }

    public function markAsPaid($id, Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string',
            'payment_date' => 'nullable|date'
        ]);

        $this->invoiceService->markAsPaid($id, $validated);
        return redirect()->back()->with('success', 'Invoice marked as paid');
    }

    public function destroy($id)
    {
        $this->invoiceService->deleteInvoice($id);
        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully');
    }
}
