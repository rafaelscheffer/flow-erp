<?php

use Illuminate\Support\Facades\Route;
use Modules\Financial\Http\Controllers\Api\V1\AccountController;
use Modules\Financial\Http\Controllers\Api\V1\CashFlowController;
use Modules\Financial\Http\Controllers\Api\V1\CostCenterController;
use Modules\Financial\Http\Controllers\Api\V1\MarkPayableAsPaidController;
use Modules\Financial\Http\Controllers\Api\V1\MarkReceivableAsPaidController;
use Modules\Financial\Http\Controllers\Api\V1\PayableController;
use Modules\Financial\Http\Controllers\Api\V1\ReceivableController;

Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function (): void {
    Route::get('accounts', [AccountController::class, 'index'])->middleware('ability:accounts.view');
    Route::get('accounts/{account}', [AccountController::class, 'show'])->middleware('ability:accounts.view');
    Route::post('accounts', [AccountController::class, 'store'])->middleware('ability:accounts.create');
    Route::put('accounts/{account}', [AccountController::class, 'update'])->middleware('ability:accounts.update');
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->middleware('ability:accounts.delete');

    Route::get('cost-centers', [CostCenterController::class, 'index'])->middleware('ability:cost-centers.view');
    Route::get('cost-centers/{cost_center}', [CostCenterController::class, 'show'])->middleware('ability:cost-centers.view');
    Route::post('cost-centers', [CostCenterController::class, 'store'])->middleware('ability:cost-centers.create');
    Route::put('cost-centers/{cost_center}', [CostCenterController::class, 'update'])->middleware('ability:cost-centers.update');
    Route::delete('cost-centers/{cost_center}', [CostCenterController::class, 'destroy'])->middleware('ability:cost-centers.delete');

    Route::get('receivables', [ReceivableController::class, 'index'])->middleware('ability:receivables.view');
    Route::get('receivables/{receivable}', [ReceivableController::class, 'show'])->middleware('ability:receivables.view');
    Route::post('receivables', [ReceivableController::class, 'store'])->middleware('ability:receivables.create');
    Route::put('receivables/{receivable}', [ReceivableController::class, 'update'])->middleware('ability:receivables.update');
    Route::delete('receivables/{receivable}', [ReceivableController::class, 'destroy'])->middleware('ability:receivables.delete');
    Route::post('receivables/{receivable}/mark-as-paid', MarkReceivableAsPaidController::class)->middleware('ability:receivables.mark-paid');

    Route::get('payables', [PayableController::class, 'index'])->middleware('ability:payables.view');
    Route::get('payables/{payable}', [PayableController::class, 'show'])->middleware('ability:payables.view');
    Route::post('payables', [PayableController::class, 'store'])->middleware('ability:payables.create');
    Route::put('payables/{payable}', [PayableController::class, 'update'])->middleware('ability:payables.update');
    Route::delete('payables/{payable}', [PayableController::class, 'destroy'])->middleware('ability:payables.delete');
    Route::post('payables/{payable}/mark-as-paid', MarkPayableAsPaidController::class)->middleware('ability:payables.mark-paid');

    Route::get('cash-flow', CashFlowController::class)->middleware('ability:cash-flow.view');
});
