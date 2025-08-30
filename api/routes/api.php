<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;

Route::get('/health', fn () => response()->json(['ok' => true]));

// Test routes protected by roles (example only)
Route::middleware(['auth:sanctum','role:admin'])->get('/admin/ping', fn () => response()->json(['pong' => 'admin']));
Route::middleware(['auth:sanctum','role:franchisee'])->get('/franchisee/ping', fn () => response()->json(['pong' => 'franchisee']));

// CRUD REST
Route::apiResource('clients', ClientController::class);
Route::apiResource('plats', PlatController::class);
Route::apiResource('evenements', EvenementController::class);
Route::apiResource('commandes', CommandeController::class);

// Paiement simulé + webhook (si tu utilises cette partie)
Route::post('/payments/simulate', [PaymentController::class, 'simulate']);
Route::post('/webhooks/payments', [WebhookController::class, 'payments']);
