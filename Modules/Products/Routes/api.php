<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\Api\V1\BrandController;
use Modules\Products\Http\Controllers\Api\V1\ProductCategoryController;
use Modules\Products\Http\Controllers\Api\V1\ProductCollectionController;
use Modules\Products\Http\Controllers\Api\V1\ProductController;

Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function (): void {
    Route::get('brands', [BrandController::class, 'index'])->middleware('ability:brands.view');
    Route::get('brands/{brand}', [BrandController::class, 'show'])->middleware('ability:brands.view');
    Route::post('brands', [BrandController::class, 'store'])->middleware('ability:brands.create');
    Route::put('brands/{brand}', [BrandController::class, 'update'])->middleware('ability:brands.update');
    Route::delete('brands/{brand}', [BrandController::class, 'destroy'])->middleware('ability:brands.delete');

    Route::get('collections', [ProductCollectionController::class, 'index'])->middleware('ability:collections.view');
    Route::get('collections/{collection}', [ProductCollectionController::class, 'show'])->middleware('ability:collections.view');
    Route::post('collections', [ProductCollectionController::class, 'store'])->middleware('ability:collections.create');
    Route::put('collections/{collection}', [ProductCollectionController::class, 'update'])->middleware('ability:collections.update');
    Route::delete('collections/{collection}', [ProductCollectionController::class, 'destroy'])->middleware('ability:collections.delete');

    Route::get('categories', [ProductCategoryController::class, 'index'])->middleware('ability:categories.view');
    Route::get('categories/{category}', [ProductCategoryController::class, 'show'])->middleware('ability:categories.view');
    Route::post('categories', [ProductCategoryController::class, 'store'])->middleware('ability:categories.create');
    Route::put('categories/{category}', [ProductCategoryController::class, 'update'])->middleware('ability:categories.update');
    Route::delete('categories/{category}', [ProductCategoryController::class, 'destroy'])->middleware('ability:categories.delete');

    Route::get('products', [ProductController::class, 'index'])->middleware('ability:products.view');
    Route::get('products/{product}', [ProductController::class, 'show'])->middleware('ability:products.view');
    Route::post('products', [ProductController::class, 'store'])->middleware('ability:products.create');
    Route::put('products/{product}', [ProductController::class, 'update'])->middleware('ability:products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('ability:products.delete');
});
