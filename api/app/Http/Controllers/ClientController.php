<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    // Liste des clients
    public function index()
    {
        return response()->json(Client::all());
    }

    // Créer un client
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'password' => 'required|string|min:6',
        ]);

        $client = Client::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($client, 201);
    }

    // Afficher un client
    public function show(Client $client)
    {
        return response()->json($client);
    }

    // Modifier un client
    public function update(Request $request, Client $client)
    {
        $client->update($request->all());
        return response()->json($client);
    }

    // Supprimer un client
    public function destroy(Client $client)
    {
        $client->delete();
        return response()->json(null, 204);
    }
}
