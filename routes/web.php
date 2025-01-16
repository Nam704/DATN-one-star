<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\AttributeController;
use App\Http\Controllers\Web\ProductController;


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

    // Attributes Routes
    Route::prefix('attributes')->controller(AttributeController::class)->name('attributes.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::get('/trash', 'trash')->name('trash');
        Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
        Route::post('/{id}/restore', 'restore')->name('restore');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Brands Routes
    Route::prefix('brands')->controller(BrandController::class)->name('brands.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::get('/trash', 'trash')->name('trash');
        Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
        Route::post('/{id}/restore', 'restore')->name('restore');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Products Routes
    Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
    });
});
