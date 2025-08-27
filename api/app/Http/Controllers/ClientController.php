<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // GET /api/clients
    public function index(): JsonResponse
    {
        return response()->json(Client::orderBy('id')->get());
    }

    // GET /api/clients/{client}
    public function show(Client $client): JsonResponse
    {
        return response()->json($client);
    }

    // POST /api/clients
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:clients,email',
        ]);

        $client = Client::create($validated);
        return response()->json($client, 201);
    }

    // PUT /api/clients/{client}
    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:clients,email,'.$client->id,
        ]);

        $client->update($validated);
        return response()->json($client);
    }

    // DELETE /api/clients/{client}
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();
        return response()->json(['deleted' => true]);
    }
}
