<?php

use App\Http\Controllers\API\AttributeController;
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
