<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EvenementController extends Controller
{
    private function filter(Evenement $model, array $data): array
    {
        $cols = Schema::getColumnListing($model->getTable());
        $allowed = array_flip(array_diff($cols, ['id','created_at','updated_at']));
        return array_intersect_key($data, $allowed);
    }

    public function index(): JsonResponse
    {
        return response()->json(Evenement::orderByDesc('id')->get());
    }

    public function show(Evenement $evenement): JsonResponse
    {
        return response()->json($evenement);
    }

    public function store(Request $request): JsonResponse
    {
        // Validation très souple -> adapte si tu connais exactement tes colonnes
        $request->validate([
            'titre' => 'sometimes|string|max:150',
            'nom'   => 'sometimes|string|max:150',
            'date'  => 'sometimes|date',
        ]);

        $data = $this->filter(new Evenement, $request->all());
        $evt = Evenement::create($data);

        return response()->json($evt, 201);
    }

    public function update(Request $request, Evenement $evenement): JsonResponse
    {
        $request->validate([
            'titre' => 'sometimes|string|max:150',
            'nom'   => 'sometimes|string|max:150',
            'date'  => 'sometimes|date',
        ]);

        $data = $this->filter($evenement, $request->all());
        $evenement->update($data);

        return response()->json($evenement);
    }

    public function destroy(Evenement $evenement): JsonResponse
    {
        $evenement->delete();
        return response()->json(['deleted' => true]);
    }
}
