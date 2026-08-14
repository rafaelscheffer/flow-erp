<?php

use Illuminate\Support\Facades\Route;
use Modules\Customers\Http\Controllers\Api\V1\CustomerController;

Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function (): void {
    Route::get('customers', [CustomerController::class, 'index'])->middleware('ability:customers.view');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->middleware('ability:customers.view');
    Route::post('customers', [CustomerController::class, 'store'])->middleware('ability:customers.create');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->middleware('ability:customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->middleware('ability:customers.delete');
});
