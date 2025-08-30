<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeLigne;
use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $q = Commande::query()->with('lignes');

        if ($request->filled('client_id')) {
            $q->where('client_id', (int)$request->input('client_id'));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        return response()->json($q->orderByDesc('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'status'    => ['nullable', 'string', Rule::in(['brouillon','validee','annulee'])],
            'lignes'    => 'required|array|min:1',
            'lignes.*.plat_id'  => 'required|exists:plats,id',
            'lignes.*.quantite' => 'required|integer|min:1',
        ]);

        $commande = null;

        DB::transaction(function () use (&$commande, $data) {
            $commande = Commande::create([
                'client_id' => $data['client_id'],
                'status'    => $data['status'] ?? 'brouillon',
                'paye'      => 0,
                'total_ht'  => 0,
                'total_ttc' => 0,
            ]);

            $this->replaceLignes($commande, $data['lignes']);
        });

        return response()->json($commande->load('lignes'), Response::HTTP_CREATED);
    }

    public function show(Commande $commande)
    {
        return response()->json($commande->load('lignes'));
    }

    public function update(Request $request, Commande $commande)
    {
        $data = $request->validate([
            'client_id' => 'sometimes|exists:clients,id',
            'status'    => ['sometimes','string', Rule::in(['brouillon','validee','annulee'])],
            'paye'      => 'sometimes|boolean',
            'lignes'    => 'sometimes|array|min:1',
            'lignes.*.plat_id'  => 'required_with:lignes|exists:plats,id',
            'lignes.*.quantite' => 'required_with:lignes|integer|min:1',
        ]);

        DB::transaction(function () use ($commande, $data) {
            if (array_key_exists('client_id', $data)) $commande->client_id = $data['client_id'];
            if (array_key_exists('status', $data))    $commande->status    = $data['status'];
            if (array_key_exists('paye', $data))      $commande->paye      = $data['paye'];
            $commande->save();

            if (array_key_exists('lignes', $data)) {
                $this->replaceLignes($commande, $data['lignes']);
            } else {
                $this->recalcTotals($commande);
            }
        });

        return response()->json($commande->load('lignes'));
    }

    public function destroy(Commande $commande)
    {
        $commande->lignes()->delete(); // si pas de FK ON DELETE CASCADE
        $commande->delete();

        return response()->json(['deleted' => true]);
    }

    /** Remplace toutes les lignes de la commande, puis recalcule les totaux */
    private function replaceLignes(Commande $commande, array $lignes): void
    {
        $commande->lignes()->delete();

        foreach ($lignes as $l) {
            $plat = Plat::find($l['plat_id']);
            if (!$plat) continue;

            $qte  = (int)$l['quantite'];
            $pu   = (float)$plat->prix;
            $tot  = round($pu * $qte, 2);

            $commande->lignes()->create([
                'plat_id'       => $plat->id,
                'quantite'      => $qte,
                'prix_unitaire' => $pu,
                'total_ligne'   => $tot,
            ]);
        }

        $this->recalcTotals($commande);
    }

    /** total_ht = somme(total_ligne), total_ttc = total_ht (ajoute TVA si besoin) */
    private function recalcTotals(Commande $commande): void
    {
        $commande->load('lignes');
        $totalHt = $commande->lignes->sum('total_ligne');

        $commande->total_ht  = round((float)$totalHt, 2);
        $commande->total_ttc = round((float)$totalHt, 2); // ajoute *1.2 si TVA 20%
        $commande->save();
    }
}
