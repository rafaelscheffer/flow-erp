<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Api\V1\StockBalanceController;
use Modules\Inventory\Http\Controllers\Api\V1\StockLocationController;
use Modules\Inventory\Http\Controllers\Api\V1\StockMovementController;
use Modules\Inventory\Http\Controllers\Api\V1\StockReservationController;

Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function (): void {
    Route::get('locations', [StockLocationController::class, 'index'])->middleware('ability:locations.view');
    Route::get('locations/{location}', [StockLocationController::class, 'show'])->middleware('ability:locations.view');
    Route::post('locations', [StockLocationController::class, 'store'])->middleware('ability:locations.create');
    Route::put('locations/{location}', [StockLocationController::class, 'update'])->middleware('ability:locations.update');
    Route::delete('locations/{location}', [StockLocationController::class, 'destroy'])->middleware('ability:locations.delete');

    Route::get('movements', [StockMovementController::class, 'index'])->middleware('ability:movements.view');
    Route::get('movements/{movement}', [StockMovementController::class, 'show'])->middleware('ability:movements.view');
    Route::post('movements', [StockMovementController::class, 'store'])->middleware('ability:movements.create');

    Route::get('balances', [StockBalanceController::class, 'index'])->middleware('ability:balances.view');
    Route::get('balances/{balance}', [StockBalanceController::class, 'show'])->middleware('ability:balances.view');

    Route::get('reservations', [StockReservationController::class, 'index'])->middleware('ability:reservations.view');
    Route::get('reservations/{reservation}', [StockReservationController::class, 'show'])->middleware('ability:reservations.view');
    Route::post('reservations', [StockReservationController::class, 'store'])->middleware('ability:reservations.create');
    Route::put('reservations/{reservation}', [StockReservationController::class, 'update'])->middleware('ability:reservations.update');
});
