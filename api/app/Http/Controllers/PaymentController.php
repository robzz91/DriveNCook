<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * POST /api/payments/simulate
     *
     * Corps (JSON):
     * {
     *   "commande_id": 1,
     *   "method": "card",           // optionnel: card|cash|other
     *   "force": "approved"         // optionnel: approved|refused|pending
     * }
     */
    public function simulate(Request $request)
    {
        $data = $request->validate([
            'commande_id' => ['required', 'integer', 'exists:commandes,id'],
            'method'      => ['nullable', 'string', 'in:card,cash,other'],
            'force'       => ['nullable', 'string', 'in:approved,refused,pending'],
        ]);

        $commande = Commande::findOrFail($data['commande_id']);

        // Résultat : forcé si fourni, sinon 80% approved / 20% refused
        $status = $data['force'] ?? (mt_rand(0, 100) < 80 ? 'approved' : 'refused');

        $montant = $commande->total_ttc ?? $commande->total_ht ?? 0.0;

        // Création du paiement local
        $paiement = Paiement::create([
            'commande_id' => $commande->id,
            'montant'     => $montant,
            'statut'      => $status,
            'meta'        => [
                'method' => $data['method'] ?? 'card',
                'txn'    => (string) Str::uuid(),
            ],
            'paid_at'     => $status === 'approved' ? now() : null,
        ]);

        // Payload “webhook”
        $payload = [
            'event' => "paiement.$status",
            'data'  => [
                'paiement_id'  => $paiement->id,
                'commande_id'  => $commande->id,
                'montant'      => $paiement->montant,
                'paid_at'      => optional($paiement->paid_at)->toISOString(),
            ],
        ];

        // Appel interne du contrôleur de webhook (pas de requête HTTP externe)
        try {
            app()->call([WebhookController::class, 'payments'], ['request' => new Request($payload)]);
        } catch (\Throwable $e) {
            Log::warning('Webhook dispatch failed: '.$e->getMessage());
        }

        return response()->json([
            'ok'       => true,
            'paiement' => $paiement,
        ], 201);
    }
}
