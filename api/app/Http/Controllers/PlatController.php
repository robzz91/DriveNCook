<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\Request;

class PlatController extends Controller
{
    public function index()
    {
        return response()->json(Plat::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0'
        ]);

        $plat = Plat::create($request->all());

        return response()->json($plat, 201);
    }

    public function show($id)
    {
        $plat = Plat::findOrFail($id);
        return response()->json($plat, 200);
    }

    public function update(Request $request, $id)
    {
        $plat = Plat::findOrFail($id);
        $plat->update($request->all());
        return response()->json($plat, 200);
    }

    public function destroy($id)
    {
        Plat::destroy($id);
        return response()->json(['message' => 'Plat supprimé'], 200);
    }
}
