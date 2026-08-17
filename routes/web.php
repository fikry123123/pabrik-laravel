<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\ProductionController;
use App\Http\Controllers\Web\RecipeController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes – PabrikPro
|--------------------------------------------------------------------------
*/

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',      [AuthController::class, 'showLogin'])->name('login');
    // GET /login juga menampilkan halaman login supaya reload URL /login
    // tidak menghasilkan error 405 (Method Not Allowed).
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login',[AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Protected ───────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory (admin & editor)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');

        Route::post('/')->middleware('feature:bahan_baku,can_create')->uses([InventoryController::class, 'store'])->name('store');
        Route::put('/{inventory}')->middleware('feature:bahan_baku,can_update')->uses([InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}')->middleware('feature:bahan_baku,can_delete')->uses([InventoryController::class, 'destroy'])->name('destroy');
    });

    // Resep / BOM (admin & editor)
    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('index');

        Route::post('/')->middleware('feature:resep,can_create')->uses([RecipeController::class, 'store'])->name('store');
        Route::put('/{recipe}')->middleware('feature:resep,can_update')->uses([RecipeController::class, 'update'])->name('update');
        Route::delete('/{recipe}')->middleware('feature:resep,can_delete')->uses([RecipeController::class, 'destroy'])->name('destroy');
    });

    // Produksi
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/',             [ProductionController::class, 'index'])->name('index');
        Route::get('/outbound',     [ProductionController::class, 'outbound'])->name('outbound');

        Route::post('/start')->middleware('feature:produksi,can_create')->uses([ProductionController::class, 'start'])->name('start');
        Route::post('/complete/{wip}')->middleware('feature:produksi,can_update')->uses([ProductionController::class, 'complete'])->name('complete');
        Route::put('/outbound/{barangKeluar}')->middleware('feature:barang_keluar,can_update')->uses([ProductionController::class, 'updateOutbound'])->name('outbound.update');
        Route::delete('/outbound/{barangKeluar}')->middleware('feature:barang_keluar,can_delete')->uses([ProductionController::class, 'destroyOutbound'])->name('outbound.destroy');
    });

    // Users (admin only)
    Route::middleware('role:admin')
        ->prefix('users')->name('users.')->group(function () {
            Route::get('/',          [UserController::class, 'index'])->name('index');
            Route::post('/',         [UserController::class, 'store'])->name('store');
            Route::put('/{user}',    [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

            // Permission Management
            Route::put('/permissions/{user}', [UserController::class, 'updateUserPermissions'])->name('permissions.update');
        });
});
