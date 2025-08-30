<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function index()
    {
        return response()->json(Commande::with('lignes')->get(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date'
        ]);

        $commande = Commande::create($request->all());

        return response()->json($commande, 201);
    }

    public function show($id)
    {
        $commande = Commande::with('lignes')->findOrFail($id);
        return response()->json($commande, 200);
    }

    public function update(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);
        $commande->update($request->all());
        return response()->json($commande, 200);
    }

    public function destroy($id)
    {
        Commande::destroy($id);
        return response()->json(['message' => 'Commande supprimée'], 200);
    }
}
