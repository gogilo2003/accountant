<?php
// app/Services/TransactionService.php
namespace App\Services;

use App\Interfaces\TransactionRepositoryInterface;
use App\Interfaces\InvoiceRepositoryInterface;

class TransactionService
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}

    // Basic CRUD Operations
    public function getAllTransactions(array $filters = [])
    {
        return isset($filters['search'])
            ? $this->transactionRepository->search($filters['search'])
            : $this->transactionRepository->paginate();
    }

    public function getTransactionById($id)
    {
        return $this->transactionRepository->find($id);
    }

    public function createTransaction(array $data)
    {
        // Handle invoice payment transactions
        if (isset($data['invoice_id']) && $data['type'] === 'payment') {
            $this->invoiceRepository->markAsPaid($data['invoice_id'], $data);
        }

        return $this->transactionRepository->create($data);
    }

    public function updateTransaction($id, array $data)
    {
        return $this->transactionRepository->update($id, $data);
    }

    public function deleteTransaction($id)
    {
        return $this->transactionRepository->delete($id);
    }

    // Financial Operations
    public function getRecentTransactions($limit = 5)
    {
        return $this->transactionRepository->getRecent($limit);
    }

    public function getTransactionSummary($period = 'monthly')
    {
        return $this->transactionRepository->getSummary($period);
    }

    public function recordPayment(array $paymentData)
    {
        $paymentData['type'] = 'payment';
        $paymentData['direction'] = 'credit';
        $paymentData['transaction_date'] = $paymentData['transaction_date'] ?? now();

        if (isset($paymentData['invoice_id'])) {
            $this->invoiceRepository->markAsPaid(
                $paymentData['invoice_id'],
                $paymentData
            );
        }

        return $this->transactionRepository->create($paymentData);
    }
}
