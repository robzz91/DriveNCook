<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $rows = DB::table('commandes')
                ->join('clients', 'commandes.client_id', '=', 'clients.id')
                ->select(
                    'commandes.id',
                    'commandes.client_id',
                    'commandes.status',
                    'commandes.paye',
                    'commandes.total_ht',
                    'commandes.total_ttc',
                    'commandes.created_at',
                    'clients.nom as client_nom',
                    'clients.email as client_email'
                )
                ->orderByDesc('commandes.id')
                ->get();

            return response()->json($rows);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'DB error', 'message' => $e->getMessage()], 500);
        }
    }
}
