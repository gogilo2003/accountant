<?php

// app/Services/InvoiceService.php
namespace App\Services;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;

class InvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    // Basic CRUD Operations
    public function getAllInvoices(array $filters = [])
    {
        return isset($filters['search'])
            ? $this->invoiceRepository->search($filters['search'])
            : $this->invoiceRepository->paginate();
    }

    public function getInvoiceById($id)
    {
        return $this->invoiceRepository->find($id);
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
