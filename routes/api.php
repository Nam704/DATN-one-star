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
