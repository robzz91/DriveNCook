<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlatController extends Controller
{
    public function index()
    {
        return response()->json(Plat::orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix'        => 'required|numeric|min:0',
        ]);

        $plat = Plat::create($data);

        return response()->json($plat, Response::HTTP_CREATED);
    }

    public function show(Plat $plat)
    {
        return response()->json($plat);
    }

    public function update(Request $request, Plat $plat)
    {
        $data = $request->validate([
            'nom'         => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'prix'        => 'sometimes|numeric|min:0',
        ]);

        $plat->update($data);

        return response()->json($plat);
    }

    public function destroy(Plat $plat)
    {
        $plat->delete();
        return response()->json(['deleted' => true]);
    }
}
