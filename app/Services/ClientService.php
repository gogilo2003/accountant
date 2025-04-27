<?php

// app/Services/ClientService.php
namespace App\Services;

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
            'recent_transactions' => $this->transactionRepository->getByClient($clientId, 5)
        ];
    }
}
