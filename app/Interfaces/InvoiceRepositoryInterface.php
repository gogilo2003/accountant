<?php
// app/Interfaces/InvoiceRepositoryInterface.php
namespace App\Interfaces;


interface InvoiceRepositoryInterface
{
    // Basic CRUD Operations
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    // Listing and Pagination
    public function paginate($perPage = 10);
    public function search($query);

    // Client Relationships
    public function getByClient($clientId, $limit = 5);
    public function getClientInvoicesSummary($clientId);

    // Status Management
    public function getByStatus($status);
    public function getDrafts();
    public function getSent();
    public function getPaid();
    public function getOverdue();

    // Invoice Operations
    public function getNextInvoiceNumber();
    public function markAsSent($invoiceId);
    public function markAsPaid($invoiceId, array $paymentData);

    // Financial Reporting
    public function getTotalRevenue($period = 'monthly');
    public function getOutstandingRevenue();

    // Utility Methods
    public function exists($invoiceNumber);
    public function getRecent(int $limit = 5);
}
