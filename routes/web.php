<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GoogleController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\MailController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserContronler;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Web\ImageController;
use App\Http\Controllers\Web\ProductAuditController;
use App\Http\Controllers\Web\ProductVariantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\AttributeController;
use App\Http\Controllers\Web\TemplateExportController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\AttributeValueController;


Route::get('/', function () {
    return view('admin.index');
});

Route::get('/users', function () {
    return view('admin.user.demoDataTable');
});
Route::get('/profile-admin', function () {
    return view('admin.profile');
})->name('auth.getProfileAdmin');

// Route cho đăng xuất
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('auth.logout');

// Client Routes
Route::get('/', [HomeController::class, 'index'])->name('client.home');
Route::get('/cart', [CartController::class, 'index'])->name('client.cart');
Route::put('/cart/update/{id}', [CartItemController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartItemController::class, 'removeItem'])->name('cart.remove');










Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    });

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('admin.user.demoDataTable');
    });
    
    // Profile Admin Route
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('auth.getProfileAdmin');

    // Users Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/list', function () {
            return view('admin.user.list');
        })->name('list');
    });

// Imports Routes
Route::prefix('imports')->name('imports.')->group(function () {
    Route::get('/pending', [ImportController::class, 'listPending'])->name('listPending');
    Route::get('/approved', [ImportController::class, 'listApproved'])->name('listApproved');
    Route::get('/rejected', [ImportController::class, 'listRejected'])->name('listRejected'); // Added this route
    Route::get('/list', [ImportController::class, 'list'])->name('list');
    Route::get('/add', [ImportController::class, 'getFormAdd'])->name('getFormAdd');
    Route::post('/add', [ImportController::class, 'postFormAdd'])->name('postFormAdd');
    Route::get('/edit/{id}', [ImportController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [ImportController::class, 'update'])->name('update');
    Route::get('/detail/{id}', [ImportController::class, 'detail'])->name('detail');
    Route::get('/accept/{id}', [ImportController::class, 'accept'])->name('accept');
    Route::get('/reject/{id}', [ImportController::class, 'reject'])->name('reject');
    Route::post('/upload', [ImportController::class, 'upload'])->name('upload');
});
 // Suppliers Routes
 Route::prefix('suppliers')->name('suppliers.')->group(function () {
    Route::get('/list', [SupplierController::class, 'list'])->name('list');
    Route::get('/add', [SupplierController::class, 'getFormAdd'])->name('getFormAdd');
    Route::post('/add', [SupplierController::class, 'postFormAdd'])->name('postFormAdd');
    Route::get('/edit/{id}', [SupplierController::class, 'getFormUpdate'])->name('getFormUpdate');
    Route::post('/edit/{id}', [SupplierController::class, 'postFormUpdate'])->name('postFormUpdate');
    Route::get('/delete/{id}', [SupplierController::class, 'delete'])->name('delete');
});
    // Route cho trang My Account
    

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
    Route::group([
        'prefix' => 'products',
        'as' => 'products.'
    ], function () {
        Route::get('list-product', [ProductController::class, 'listProduct'])->name('listProduct');
        Route::get('add-product', [ProductController::class, 'addProduct'])->name('addProduct');
        Route::post('add-product', [ProductController::class, 'addPostProduct'])->name('addPostProduct');
        Route::get('edit-product/{id}', [ProductController::class, 'editProduct'])->name('editProduct');
        Route::put('edit-product/{id}', [ProductController::class, 'editPutProduct'])->name('editPutProduct');
        Route::delete('delete-product/{id}', [ProductController::class, 'deleteProduct'])->name('deleteProduct');
    });
});
