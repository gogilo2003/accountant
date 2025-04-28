<?php

// app/Services/InvoiceService.php
namespace App\Services;

use App\Repositories\InvoiceRepository;
use App\Repositories\TransactionRepository;
use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;

class InvoiceService
{
    protected InvoiceRepository $invoiceRepository;
    protected TransactionRepository $transactionRepository;
    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->transactionRepository = $transactionRepository;
    }

    // Basic CRUD Operations
    public function getAllInvoices(array $filters = [], $relations = [])
    {
        $relations = array_merge(['client', 'transactions'], $relations);
        return isset($filters['search'])
            ? $this->invoiceRepository->search($filters['search'], $relations)
            : $this->invoiceRepository->paginate(null, $relations);
    }

    public function getInvoiceById($id, $relations = [])
    {
        return $this->invoiceRepository->find($id, $relations);
    }

    public function createInvoice(array $data)
    {
        return $this->invoiceRepository->create($data);
    }

    public function updateInvoice($id, array $data)
    {
        return $this->invoiceRepository->update($id, $data);
    }

    public function deleteInvoice($id)
    {
        return $this->invoiceRepository->delete($id);
    }

    // Status Management
    public function markAsDraft($invoiceId)
    {
        return $this->invoiceRepository->update($invoiceId, ['status' => 'draft']);
    }

    public function markAsSent($invoiceId)
    {
        return $this->invoiceRepository->markAsSent($invoiceId);
    }

    public function markAsPaid($invoiceId, array $paymentData)
    {
        return $this->invoiceRepository->markAsPaid($invoiceId, $paymentData);
    }

    public function markAsCancelled($invoiceId)
    {
        return $this->invoiceRepository->update($invoiceId, ['status' => 'cancelled']);
    }

    // Financial Operations
    public function getInvoiceTransactions($invoiceId)
    {
        return $this->transactionRepository->getByInvoice($invoiceId);
    }

    public function generateInvoiceNumber()
    {
        return $this->invoiceRepository->getNextInvoiceNumber();
    }
}
