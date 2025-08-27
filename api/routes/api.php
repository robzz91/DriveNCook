<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\EvenementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Endpoints JSON consommés par le front (Vite).
*/

Route::get('/health', fn () => ['ok' => true]);

Route::get('/clients', [ClientController::class, 'index']);
Route::get('/plats', [PlatController::class, 'index']);
Route::get('/commandes', [CommandeController::class, 'index']);
Route::get('/evenements', [EvenementController::class, 'index']);
