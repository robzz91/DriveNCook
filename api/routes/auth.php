<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\PaymentController;

Route::get('/health', fn () => response()->json(['ok' => true]));

Route::apiResource('clients', ClientController::class);
Route::apiResource('plats', PlatController::class);
Route::apiResource('evenements', EvenementController::class);
Route::apiResource('commandes', CommandeController::class);

// --- Simulation de paiement ---
Route::post('/payments/simulate', [PaymentController::class, 'simulate']);
