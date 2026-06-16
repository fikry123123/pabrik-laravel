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

        Route::middleware('role:admin,editor')->group(function () {
            Route::post('/',                          [InventoryController::class, 'store'])->name('store');
            Route::put('/{inventory}',                [InventoryController::class, 'update'])->name('update');
            Route::delete('/{inventory}',             [InventoryController::class, 'destroy'])->name('destroy');
        });
    });

    // Resep / BOM (admin & editor)
    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('index');

        Route::middleware('role:admin,editor')->group(function () {
            Route::post('/',             [RecipeController::class, 'store'])->name('store');
            Route::put('/{recipe}',      [RecipeController::class, 'update'])->name('update');
            Route::delete('/{recipe}',   [RecipeController::class, 'destroy'])->name('destroy');
        });
    });

    // Produksi
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/',             [ProductionController::class, 'index'])->name('index');
        Route::get('/outbound',     [ProductionController::class, 'outbound'])->name('outbound');

        Route::middleware('role:admin,editor')->group(function () {
            Route::post('/start',        [ProductionController::class, 'start'])->name('start');
            Route::post('/complete/{wip}',[ProductionController::class, 'complete'])->name('complete');
        });
    });

    // Users (admin only)
    Route::middleware('role:admin')
        ->prefix('users')->name('users.')->group(function () {
            Route::get('/',          [UserController::class, 'index'])->name('index');
            Route::post('/',         [UserController::class, 'store'])->name('store');
            Route::put('/{user}',    [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
});
