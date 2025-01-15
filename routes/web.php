<?php

use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Web\AttributeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GoogleController;
use App\Http\Controllers\Web\ImageController;
use App\Http\Controllers\Web\ProductAuditController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserContronler;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\MailController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('admin.index');
});
Route::prefix('auth/')->name('auth.')->group(
    function () {
        Route::controller(GoogleController::class)->group(function () {
            Route::get('google', 'redirectToGoogle')->name('google');
            Route::get('google/callback', 'handleGoogleCallback');
        });
        Route::controller(AuthController::class)->group(function () {
            Route::get('login', 'getFormLogin')->name('getFormLogin');
            Route::post('login', 'login')->name('login');

            Route::get('register', 'getFormRegister')->name('getFormRegister');
            Route::post('register', 'register')->name('register');
        });
    }
);




Route::prefix('admin')->name('admin.')->group(

    function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'dashboard')->name('dashboard');
        });
        Route::prefix('users')->name('user.')->controller(UserContronler::class)->group(
            function () {
                Route::get('/', 'list')->name('list');
            }
        );
        Route::prefix('suppliers')->controller(SupplierController::class)->name('suppliers.')->group(
            function () {
                Route::get('add', 'getFormAdd')->name('getFormAdd');
                Route::get('edit/{id}', 'getFormUpdate')->name('getFormUpdate');
                Route::get('/', 'list')->name('list');
                Route::get('lockOrActive/{id}', 'lockOrActive')->name('lockOrActive');
                Route::post('add', 'add')->name('add');
                Route::post('edit/{id}', 'edit')->name('edit');
            }
        );

        Route::prefix('images')->name('images.')->controller(ImageController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('update/{id}', 'update')->name('update');
            Route::get('destroy/{id}', 'destroy')->name('destroy');
            Route::get('show/{id}', 'show')->name('show');
        });
        Route::prefix('product_audits')->name('product_audits.')->controller(ProductAuditController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('update/{id}', 'update')->name('update');
            Route::get('destroy/{id}', 'destroy')->name('destroy');
            Route::get('show/{id}', 'show')->name('show');
        });
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
        Route::group([
            'prefix' => 'categories',
            'as' => 'categories.'
        ], function () {
            Route::get('list-category', [CategoryController::class, 'listCategory'])->name('listCategory');
            Route::get('add-category', [CategoryController::class, 'addCategory'])->name('addCategory');
            Route::post('add-category', [CategoryController::class, 'addPostCategory'])->name('addPostCategory');
            Route::get('edit-category/{id}', [CategoryController::class, 'editCategory'])->name('editCategory');
            Route::put('edit-category/{id}', [CategoryController::class, 'editPutCategory'])->name('editPutCategory');
            Route::delete('delete-category/{id}', [CategoryController::class, 'deleteCategory'])->name('deleteCategory');

            Route::post('add-category', [CategoryController::class, 'addCategoryProduct'])->name('addCategoryProduct');
        });


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

        Route::group([
            'prefix' => 'productvariant',
            'as' => 'productvariant.'
        ], function () {
            Route::get('list-productvariant', [ProductVariantController::class, 'listProductVariant'])->name('listProductVariant');
            Route::get('add-productvariant', [ProductVariantController::class, 'addProductVariant'])->name('addProductVariant');
            Route::post('add-productvariant', [ProductVariantController::class, 'addPostProductVariant'])->name('addPostProductVariant');
            Route::get('edit-productvariant/{id}', [ProductVariantController::class, 'editProductVariant'])->name('editProductVariant');
            Route::put('edit-productvariant/{id}', [ProductVariantController::class, 'editPutProductVariant'])->name('editPutProductVariant');
            Route::delete('delete-productvariant/{id}', [ProductVariantController::class, 'deleteProductVariant'])->name('deleteProductVariant');
        });
    }
);
Route::prefix('client')->name('client.')->group(
    function () {
        Route::prefix('users')->name('user.')->group(
            function () {}
        );
    }
);
