<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(Client::orderBy('id')->get(), 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'   => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'],
        ]);

        $client = Client::create($data);

        return response()->json($client, 201);
    }

    public function show(int $id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }
        return response()->json($client, 200);
    }

    public function update(Request $request, int $id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $data = $request->validate([
            'nom'   => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:clients,email,' . $client->id],
        ]);

        $client->fill($data)->save();

        return response()->json($client, 200);
    }

    public function destroy(int $id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $client->delete();

        return response()->json(['deleted' => true], 200);
    }
}
