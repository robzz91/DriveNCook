<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeLigne;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CommandeController extends Controller
{
    private function setStatusField(array &$data)
    {
        // s'adapte si la colonne s'appelle 'status' ou 'statut'
        $cols = Schema::getColumnListing('commandes');
        if (array_key_exists('status', $data) && in_array('status', $cols)) return;
        if (array_key_exists('statut', $data) && in_array('statut', $cols)) return;
        if (in_array('status', $cols) && isset($data['statut'])) { $data['status'] = $data['statut']; unset($data['statut']); }
        if (in_array('statut', $cols) && isset($data['status'])) { $data['statut'] = $data['status']; unset($data['status']); }
    }

    private function totalsFromLines(array $lignes): array
    {
        $total_ht = 0;
        foreach ($lignes as $l) {
            $q = (int)($l['quantite'] ?? 0);
            $pu = (float)($l['prix_unitaire'] ?? 0);
            $total_ht += $q * $pu;
        }
        // si tu as une TVA, adapte ici
        $total_ttc = $total_ht;
        return compact('total_ht','total_ttc');
    }

    public function index(): JsonResponse
    {
        $cmds = Commande::with(['client','lignes.plat'])->orderByDesc('id')->get();
        return response()->json($cmds);
    }

    public function show(Commande $commande): JsonResponse
    {
        $commande->load(['client','lignes.plat']);
        return response()->json($commande);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'status'    => 'nullable|string|max:50',
            'statut'    => 'nullable|string|max:50',
            'lignes'    => 'required|array|min:1',
            'lignes.*.plat_id'       => 'required|integer|exists:plats,id',
            'lignes.*.quantite'      => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $this->setStatusField($validated);

        DB::beginTransaction();
        try {
            $totaux = $this->totalsFromLines($validated['lignes']);
            $commande = Commande::create(array_merge(
                ['client_id' => $validated['client_id']],
                $totaux,
                array_intersect_key($validated, array_flip(['status','statut']))
            ));

            foreach ($validated['lignes'] as $l) {
                CommandeLigne::create([
                    'commande_id'  => $commande->id,
                    'plat_id'      => $l['plat_id'],
                    'quantite'     => $l['quantite'],
                    'prix_unitaire'=> $l['prix_unitaire'],
                    'total_ligne'  => $l['quantite'] * $l['prix_unitaire'],
                ]);
            }

            $commande->load(['client','lignes.plat']);
            DB::commit();
            return response()->json($commande, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Commande $commande): JsonResponse
    {
        $validated = $request->validate([
            'status'    => 'sometimes|string|max:50',
            'statut'    => 'sometimes|string|max:50',
            'lignes'    => 'sometimes|array|min:1',
            'lignes.*.plat_id'       => 'required_with:lignes|integer|exists:plats,id',
            'lignes.*.quantite'      => 'required_with:lignes|integer|min:1',
            'lignes.*.prix_unitaire' => 'required_with:lignes|numeric|min:0',
        ]);

        $this->setStatusField($validated);

        DB::beginTransaction();
        try {
            // Maj du statut si fourni
            $fields = array_intersect_key($validated, array_flip(['status','statut']));
            if (!empty($fields)) $commande->update($fields);

            // Si nouvelles lignes -> on remplace, puis recalcule les totaux
            if (!empty($validated['lignes'])) {
                $commande->lignes()->delete();
                foreach ($validated['lignes'] as $l) {
                    CommandeLigne::create([
                        'commande_id'  => $commande->id,
                        'plat_id'      => $l['plat_id'],
                        'quantite'     => $l['quantite'],
                        'prix_unitaire'=> $l['prix_unitaire'],
                        'total_ligne'  => $l['quantite'] * $l['prix_unitaire'],
                    ]);
                }
                $totaux = $this->totalsFromLines($validated['lignes']);
                $commande->update($totaux);
            }

            $commande->load(['client','lignes.plat']);
            DB::commit();
            return response()->json($commande);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Commande $commande): JsonResponse
    {
        DB::transaction(function () use ($commande) {
            $commande->lignes()->delete();
            $commande->delete();
        });

        return response()->json(['deleted' => true]);
    }
}
