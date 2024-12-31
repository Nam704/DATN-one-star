<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.index');
});

Route::get('/users', function () {
    return view('admin.user.demoDataTable');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    });
    
    Route::get('/users', function () {
        return view('admin.user.demoDataTable');
    });

    Route::get('attributes/{id}/edit', [\App\Http\Controllers\Web\AttributeController::class, 'edit'])->name('attributes.edit');
    Route::put('attributes/{id}', [\App\Http\Controllers\Web\AttributeController::class, 'update'])->name('attributes.update');

    Route::post('attributes/{id}/toggle-status', [\App\Http\Controllers\Web\AttributeController::class, 'toggleStatus'])->name('attributes.toggle-status');

    Route::get('attributes/trash', [\App\Http\Controllers\Web\AttributeController::class, 'trash'])->name('attributes.trash');
    Route::delete('attributes/{id}/force-delete', [\App\Http\Controllers\Web\AttributeController::class, 'forceDelete'])->name('attributes.force-delete');
    Route::post('attributes/{id}/restore', [\App\Http\Controllers\Web\AttributeController::class, 'restore'])->name('attributes.restore');
    Route::delete('/attributes/{id}', [\App\Http\Controllers\Web\AttributeController::class, 'destroy'])->name('attributes.destroy');
    Route::resource('attributes', \App\Http\Controllers\Web\AttributeController::class);
});
