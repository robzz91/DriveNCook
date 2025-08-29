<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * POST /api/webhooks/payments
     * Corps:
     * {
     *   "event": "paiement.approved|paiement.refused|paiement.pending",
     *   "data": {
     *     "commande_id": 1,
     *     "paiement_id": 4,
     *     "montant": 12.5,
     *     "paid_at": "2025-08-27T11:34:00Z"
     *   }
     * }
     */
    public function payments(Request $request)
    {
        $payload = $request->validate([
            'event'                => ['required', 'string'],
            'data.commande_id'     => ['required', 'integer', 'exists:commandes,id'],
            'data.paiement_id'     => ['nullable', 'integer'],
            'data.montant'         => ['nullable', 'numeric'],
            'data.paid_at'         => ['nullable', 'date'],
        ]);

        $commande = Commande::findOrFail($payload['data']['commande_id']);

        // Mise à jour du statut de la commande selon l’événement
        if (str_contains($payload['event'], 'approved')) {
            $commande->statut = 'validee';
        } elseif (str_contains($payload['event'], 'refused')) {
            $commande->statut = 'annulee';
        } else {
            $commande->statut = 'en_cours';
        }

        $commande->save();

        return response()->json(['ok' => true]);
    }
}
