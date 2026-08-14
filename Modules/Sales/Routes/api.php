<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\Api\V1\ConfirmOrderController;
use Modules\Sales\Http\Controllers\Api\V1\OrderController;

Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function (): void {
    Route::get('orders', [OrderController::class, 'index'])->middleware('ability:orders.view');
    Route::get('orders/{order}', [OrderController::class, 'show'])->middleware('ability:orders.view');
    Route::post('orders', [OrderController::class, 'store'])->middleware('ability:orders.create');
    Route::put('orders/{order}', [OrderController::class, 'update'])->middleware('ability:orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->middleware('ability:orders.delete');
    Route::post('orders/{order}/confirm', ConfirmOrderController::class)->middleware('ability:orders.confirm');
});
