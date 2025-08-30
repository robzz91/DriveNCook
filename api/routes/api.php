<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\EvenementController;

// 🚦 Vérification rapide de l’API
Route::get('/health', function () {
    return response()->json(['ok' => true]);
});

// 🔐 Authentification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

// 📦 Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('commandes', CommandeController::class);
    Route::apiResource('plats', PlatController::class);
    Route::apiResource('evenements', EvenementController::class);
});
