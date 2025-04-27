<?php

// app/Services/ClientService.php
namespace App\Services;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;

class ClientService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private InvoiceRepositoryInterface $invoiceRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    // Basic CRUD Operations
    public function getAllClients(array $filters = [])
    {
        return isset($filters['search'])
            ? $this->clientRepository->search($filters['search'])
            : $this->clientRepository->paginate();
    }

    public function getClientById($id)
    {
        return $this->clientRepository->find($id);
    }

    public function createClient(array $data)
    {
        return $this->clientRepository->create($data);
    }

    public function updateClient($id, array $data)
    {
        return $this->clientRepository->update($id, $data);
    }

    public function deleteClient($id)
    {
        return $this->clientRepository->delete($id);
    }

    // Financial Operations
    public function getClientBalance($clientId)
    {
        return $this->transactionRepository->getClientBalance($clientId);
    }

    public function getClientTransactions($clientId)
    {
        return $this->transactionRepository->getByClient($clientId);
    }

    public function getClientInvoices($clientId)
    {
        return $this->invoiceRepository->getByClient($clientId);
    }

    public function getClientFinancialSummary($clientId)
    {
        return [
            'balance' => $this->getClientBalance($clientId),
            'invoices' => $this->invoiceRepository->getClientInvoicesSummary($clientId),
            'recent_activity' => $this->transactionRepository->getByClient($clientId, 5)
        ];
    }
    public function getFinancialSummary($clientId)
    {
        return [
            'total_value' => $this->getClientBalance($clientId),
            'open_invoices' => $this->getOpenInvoicesCount($clientId),
            'transaction_count' => $this->getTransactionsCount($clientId),
            'recent_activity' => $this->getCombinedRecentActivity($clientId)
        ];
    }

    protected function getCombinedRecentActivity($clientId, $limit = 5)
    {
        // Get recent transactions using existing method
        $transactions = $this->transactionRepository->getByClient(
            clientId: $clientId,
            perPage: $limit
        );

        // Get recent invoices
        $invoices = $this->invoiceRepository->getByClient($clientId, $limit);

        return collect()
            ->merge($transactions->map(fn($t) => [
                'type' => 'transaction',
                'id' => $t->id,
                'description' => $t->description,
                'date' => $t->transaction_date,
                'amount' => $t->amount,
                'direction' => $t->direction
            ]))
            ->merge($invoices->map(fn($i) => [
                'type' => 'invoice',
                'id' => $i->id,
                'description' => 'Invoice #' . $i->invoice_number,
                'date' => $i->created_at,
                'amount' => $i->amount,
                'status' => $i->status
            ]))
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->toArray();
    }

    public function getOpenInvoicesCount($clientId): int
    {
        return Invoice::where('client_id', $clientId)
            ->where('status', 'sent')
            ->count();
    }

    public function getTransactionsCount($clientId): int
    {
        return Transaction::where('client_id', $clientId)
            ->count();
    }
}
