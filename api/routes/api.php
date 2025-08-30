<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\AuthController;

// ✅ Routes d’authentification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// ✅ CRUD protégé par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('plats', PlatController::class);
    Route::apiResource('commandes', CommandeController::class);
    Route::apiResource('evenements', EvenementController::class);
});
