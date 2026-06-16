<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProductionController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes – PabrikPro
|--------------------------------------------------------------------------
|
| Semua endpoint dilindungi Sanctum kecuali /login.
| Role guard menggunakan RoleMiddleware yang didaftarkan sebagai 'role'.
|
| Role hierarchy:
|   admin   → semua akses
|   editor  → inventory, resep, produksi (tidak bisa kelola user)
|   reviewer→ read-only (hanya GET)
|
*/

// ─── Public ─────────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ─── Protected (semua role) ──────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Dashboard – semua role boleh lihat
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::middleware('role:admin,editor')->group(function () {
        Route::post('/inventory',       [InventoryController::class, 'store']);
        Route::put('/inventory/{inventory}',    [InventoryController::class, 'update']);
        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy']);
    });

    // Resep / BOM
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::middleware('role:admin,editor')->group(function () {
        Route::post('/recipes',           [RecipeController::class, 'store']);
        Route::put('/recipes/{recipe}',   [RecipeController::class, 'update']);
        Route::delete('/recipes/{recipe}',[RecipeController::class, 'destroy']);
    });

    // Produksi & WIP & Outbound
    Route::get('/wip',      [ProductionController::class, 'wip']);
    Route::get('/outbound', [ProductionController::class, 'outbound']);
    Route::middleware('role:admin,editor')->group(function () {
        Route::post('/production/start',          [ProductionController::class, 'start']);
        Route::post('/production/complete/{wip}', [ProductionController::class, 'complete']);
    });

    // User management – hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',          [UserController::class, 'index']);
        Route::post('/users',         [UserController::class, 'store']);
        Route::put('/users/{user}',   [UserController::class, 'update']);
        Route::delete('/users/{user}',[UserController::class, 'destroy']);
    });
});
