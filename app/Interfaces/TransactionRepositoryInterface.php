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
    public function getByClient($clientId);
    public function getByInvoice($invoiceId);
    public function getRecent($limit = 5);
    public function getSummary($period = 'monthly');
    public function getClientBalance($clientId); // Added this method
}
