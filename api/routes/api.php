<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\EvenementController;

// Déjà en place pour clients (exemple)
Route::get('/health', fn() => response()->json(['ok' => true]));

// Clients (déjà faits)
Route::apiResource('clients', ClientController::class)->only(['index','show','store','update','destroy']);

// 👉 Plats
Route::apiResource('plats', PlatController::class)->only(['index','show','store','update','destroy']);

// 👉 Commandes (+ lignes)
Route::apiResource('commandes', CommandeController::class)->only(['index','show','store','update','destroy']);

// 👉 Évènements
Route::apiResource('evenements', EvenementController::class)->only(['index','show','store','update','destroy']);
