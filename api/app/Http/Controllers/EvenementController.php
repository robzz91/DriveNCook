<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;

class EvenementController extends Controller
{
    public function index()
    {
        return response()->json(Evenement::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $evenement = Evenement::create($request->all());

        return response()->json($evenement, 201);
    }

    public function show($id)
    {
        $evenement = Evenement::findOrFail($id);
        return response()->json($evenement, 200);
    }

    public function update(Request $request, $id)
    {
        $evenement = Evenement::findOrFail($id);
        $evenement->update($request->all());
        return response()->json($evenement, 200);
    }

    public function destroy($id)
    {
        Evenement::destroy($id);
        return response()->json(['message' => 'Événement supprimé'], 200);
    }
}
