<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EvenementController extends Controller
{
    public function index()
    {
        return response()->json(Evenement::orderBy('date_evenement','desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'date_evenement' => 'required|date',
        ]);

        $evt = Evenement::create($data);

        return response()->json($evt, Response::HTTP_CREATED);
    }

    public function show(Evenement $evenement)
    {
        return response()->json($evenement);
    }

    public function update(Request $request, Evenement $evenement)
    {
        $data = $request->validate([
            'titre'          => 'sometimes|string|max:255',
            'description'    => 'sometimes|nullable|string',
            'date_evenement' => 'sometimes|date',
        ]);

        $evenement->update($data);

        return response()->json($evenement);
    }

    public function destroy(Evenement $evenement)
    {
        $evenement->delete();
        return response()->json(['deleted' => true]);
    }
}
