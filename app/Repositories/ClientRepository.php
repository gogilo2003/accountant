<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Interfaces\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function all()
    {
        return Client::all();
    }

    public function find($id)
    {
        return Client::findOrFail($id);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function update($id, array $data)
    {
        $client = $this->find($id);
        $client->update($data);
        return $client;
    }

    public function delete($id)
    {
        $client = $this->find($id);
        return $client->delete();
    }

    public function paginate($perPage = 10)
    {
        return Client::paginate($perPage);
    }

    public function search($query)
    {
        return Client::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate(10);
    }

    public function countActiveClients(): int
    {
        return Client::whereHas('transactions', function ($query) {
            $query->where('transaction_date', '>=', now()->subMonths(3));
        })->count();
    }
    public function getClientBalance($clientId): float
    {
        $credits = Transaction::where('client_id', $clientId)
            ->where('direction', 'credit')
            ->sum('amount');

        $debits = Transaction::where('client_id', $clientId)
            ->where('direction', 'debit')
            ->sum('amount');

        return $credits - $debits;
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
