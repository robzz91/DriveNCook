<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\EvenementController;

Route::get('/health', fn () => ['ok' => true]);

Route::apiResource('clients', ClientController::class);

Route::get('/plats', [PlatController::class, 'index']);
Route::get('/commandes', [CommandeController::class, 'index']);
Route::get('/evenements', [EvenementController::class, 'index']);
