<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\AttributeController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\AttributeValueController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\ImageController;
use App\Http\Controllers\Web\ImportController;

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

    // AttributeValue Routes
    Route::prefix('attribute-values')->controller(AttributeValueController::class)
        ->name('attribute-values.')->group(function () {
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
            Route::get('/attributes/{id}/values', 'getAttributeValues')->name('getAttributeValues');

    });

    // Categories Routes
Route::prefix('categories')->controller(CategoryController::class)->name('categories.')->group(function () {
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
    Route::post('/add-category-product', 'addCategoryProduct')->name('addCategoryProduct');
});

    // Images Routes
    Route::prefix('images')->name('images.')->controller(ImageController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::put('update/{id}', 'update')->name('update');
        Route::get('destroy/{id}', 'destroy')->name('destroy');
        Route::get('show/{id}', 'show')->name('show');
    });


    // Products Routes
    Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/list', 'index')->name('listProduct');
    Route::get('/{id}/edit', 'edit')->name('edit');
    Route::put('/{id}', 'update')->name('update');
    Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
    Route::get('/trash', 'trash')->name('trash');
    Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
    Route::post('/{id}/restore', 'restore')->name('restore');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

});
