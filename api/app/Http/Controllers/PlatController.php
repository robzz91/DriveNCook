<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PlatController extends Controller
{
    private function filter(Plat $model, array $data): array
    {
        $cols = Schema::getColumnListing($model->getTable());
        $allowed = array_flip(array_diff($cols, ['id','created_at','updated_at']));
        return array_intersect_key($data, $allowed);
    }

    public function index(): JsonResponse
    {
        return response()->json(Plat::orderBy('id')->get());
    }

    public function show(Plat $plat): JsonResponse
    {
        return response()->json($plat);
    }

    public function store(Request $request): JsonResponse
    {
        // Validation minimale (adaptée à ta BDD)
        $request->validate([
            'nom'   => 'required|string|max:150',
            'prix'  => 'nullable|numeric',
            'actif' => 'nullable|boolean',
        ]);

        $data = $this->filter(new Plat, $request->all());

        // conversion bool
        if (isset($data['actif'])) $data['actif'] = (int) !!$data['actif'];

        $plat = Plat::create($data);
        return response()->json($plat, 201);
    }

    public function update(Request $request, Plat $plat): JsonResponse
    {
        $request->validate([
            'nom'   => 'sometimes|required|string|max:150',
            'prix'  => 'sometimes|numeric',
            'actif' => 'sometimes|boolean',
        ]);

        $data = $this->filter($plat, $request->all());
        if (isset($data['actif'])) $data['actif'] = (int) !!$data['actif'];

        $plat->update($data);
        return response()->json($plat);
    }

    public function destroy(Plat $plat): JsonResponse
    {
        $plat->delete();
        return response()->json(['deleted' => true]);
    }
}
