<?php

namespace App\Interfaces;

interface TransactionRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15);
    public function search($query);
    public function getByClient($clientId, $startDate = null, $endDate = null, $perPage = 15);
    public function getByInvoice($invoiceId);
    public function getRecent($limit = 5);
    public function getSummary($period = 'monthly');
    public function getClientBalance($clientId); // Added this method
    // Add these new methods for reporting
    public function getTotalCredits($clientId = null, $startDate = null, $endDate = null);
    public function getTotalDebits($clientId = null, $startDate = null, $endDate = null);
    public function getPeriodicSummary($startDate, $endDate);
}
