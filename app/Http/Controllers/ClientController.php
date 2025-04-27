<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService) {}

    public function index(Request $request)
    {
        return Inertia::render('clients/Index', [
            'clients' => $this->clientService->getAllClients($request->all()),
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('clients/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string'
        ]);

        $client = $this->clientService->createClient($validated);

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client created successfully');
    }

    public function show($id)
    {
        return Inertia::render('clients/Show', [
            'client' => $this->clientService->getClientById($id),
            'financialSummary' => $this->clientService->getClientFinancialSummary($id)
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('clients/Edit', [
            'client' => $this->clientService->getClientById($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string'
        ]);

        $this->clientService->updateClient($id, $validated);

        return redirect()->route('clients.show', $id)
            ->with('success', 'Client updated successfully');
    }

    public function destroy($id)
    {
        $this->clientService->deleteClient($id);
        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully');
    }
}
