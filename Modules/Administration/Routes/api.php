<?php

use Illuminate\Support\Facades\Route;
use Modules\Administration\Http\Controllers\Api\V1\AuditLogController;
use Modules\Administration\Http\Controllers\Api\V1\AuthController;
use Modules\Administration\Http\Controllers\Api\V1\RoleController;
use Modules\Administration\Http\Controllers\Api\V1\UserController;

Route::prefix('api/v1')->middleware('api')->group(function (): void {
    Route::post('auth/token', [AuthController::class, 'token']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('users', [UserController::class, 'index'])->middleware('ability:users.view');
        Route::get('users/{user}', [UserController::class, 'show'])->middleware('ability:users.view');
        Route::post('users', [UserController::class, 'store'])->middleware('ability:users.create');
        Route::put('users/{user}', [UserController::class, 'update'])->middleware('ability:users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('ability:users.delete');

        Route::get('roles', [RoleController::class, 'index'])->middleware('ability:roles.view');
        Route::get('roles/{role}', [RoleController::class, 'show'])->middleware('ability:roles.view');
        Route::post('roles', [RoleController::class, 'store'])->middleware('ability:roles.create');
        Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('ability:roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('ability:roles.delete');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->middleware('ability:audit-logs.view');
        Route::get('audit-logs/{audit_log}', [AuditLogController::class, 'show'])->middleware('ability:audit-logs.view');
    });
});
