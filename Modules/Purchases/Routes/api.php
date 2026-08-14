<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchases\Http\Controllers\Api\V1\PurchaseOrderController;
use Modules\Purchases\Http\Controllers\Api\V1\ReceivePurchaseOrderController;
use Modules\Purchases\Http\Controllers\Api\V1\SupplierController;

Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function (): void {
    Route::get('suppliers', [SupplierController::class, 'index'])->middleware('ability:suppliers.view');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->middleware('ability:suppliers.view');
    Route::post('suppliers', [SupplierController::class, 'store'])->middleware('ability:suppliers.create');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->middleware('ability:suppliers.update');
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->middleware('ability:suppliers.delete');

    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->middleware('ability:purchase-orders.view');
    Route::get('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'show'])->middleware('ability:purchase-orders.view');
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->middleware('ability:purchase-orders.create');
    Route::put('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'update'])->middleware('ability:purchase-orders.update');
    Route::delete('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'destroy'])->middleware('ability:purchase-orders.delete');
    Route::post('purchase-orders/{purchase_order}/receive', ReceivePurchaseOrderController::class)->middleware('ability:purchase-orders.receive');
});
