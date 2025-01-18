<?php

use App\Http\Controllers\API\AttributeController;
use App\Http\Controllers\API\BrandController;
use Illuminate\Support\Facades\Route;

Route::prefix('attributes')->group(function () {
    Route::get('/', [AttributeController::class, 'index']);
    Route::post('/', [AttributeController::class, 'store']);
    Route::get('/trash', [AttributeController::class, 'trash']);
    Route::get('/{id}', [AttributeController::class, 'show']);
    Route::put('/{id}', [AttributeController::class, 'update']);
    Route::delete('/{id}', [AttributeController::class, 'destroy']);
    Route::post('/{id}/toggle-status', [AttributeController::class, 'toggleStatus']);
    Route::post('/{id}/restore', [AttributeController::class, 'restore']);
    Route::delete('/{id}/force-delete', [AttributeController::class, 'forceDelete']);
});

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::post('/', [BrandController::class, 'store']);
    Route::get('/trash', [BrandController::class, 'trash']);
    Route::get('/{id}', [BrandController::class, 'show']);
    Route::put('/{id}', [BrandController::class, 'update']);
    Route::delete('/{id}', [BrandController::class, 'destroy']);
    Route::post('/{id}/toggle-status', [BrandController::class, 'toggleStatus']);
    Route::post('/{id}/restore', [BrandController::class, 'restore']);
    Route::delete('/{id}/force-delete', [BrandController::class, 'forceDelete']);
});

Route::prefix('attribute-values')->group(function () {
    Route::get('/', [AttributeValueController::class, 'index']);
    Route::post('/', [AttributeValueController::class, 'store']);
    Route::delete('/{id}', [AttributeValueController::class, 'destroy']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/trash', [CategoryController::class, 'trash']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
    Route::post('/{id}/toggle-status', [CategoryController::class, 'toggleStatus']);
    Route::post('/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete']);
});


Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/trash', [ProductController::class, 'trash']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::post('/{id}/toggle-status', [ProductController::class, 'toggleStatus']);
    Route::post('/{id}/restore', [ProductController::class, 'restore']);
    Route::delete('/{id}/force-delete', [ProductController::class, 'forceDelete']);
});
