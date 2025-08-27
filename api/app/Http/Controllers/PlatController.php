<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PlatController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $rows = DB::table('plats')->orderBy('id')->get();
            return response()->json($rows);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'DB error', 'message' => $e->getMessage()], 500);
        }
    }
}
