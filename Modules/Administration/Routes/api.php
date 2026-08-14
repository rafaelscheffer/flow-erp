<?php

use Illuminate\Support\Facades\Route;
use Modules\Administration\Http\Controllers\Api\V1\AuthController;

Route::prefix('api/v1')->middleware('api')->group(function (): void {
    Route::post('auth/token', [AuthController::class, 'token']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
    });
});
