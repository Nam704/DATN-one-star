<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GoogleController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProductVariantController;
use App\Http\Controllers\Web\ImageController;
use App\Http\Controllers\Web\ProductAuditController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserContronler;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\MailController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\AttributeController;
use Illuminate\Support\Facades\Mail;

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

    //brand
    Route::get('brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('brands/{id}', [BrandController::class, 'update'])->name('brands.update');
    Route::post('brands/{id}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggle-status');
    Route::get('brands/trash', [BrandController::class, 'trash'])->name('brands.trash');
    Route::delete('brands/{id}/force-delete', [BrandController::class, 'forceDelete'])->name('brands.force-delete');
    Route::post('brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');
    Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::resource('brands', BrandController::class);
});



Route::get('/make-password', function () {
    return Hash::make('1234');
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
            Route::get('logout', 'logout')->name('logout');
            Route::get('forgot-password', 'getFormForgotPassword')->name('getFormForgotPassword');
            Route::post('forgot-password', 'sendPasswordResetEmail')->name('sendPasswordResetEmail');

            Route::get('reset-password/{token}', 'getfromResetPassword')->name('getfromResetPassword');
            Route::post('reset-password', 'resetPassword')->name('resetPassword');
        });
    }
);




Route::prefix('admin')->name('admin.')->group(

    function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
        });


        // Route::prefix('categorys')->name('categorys')->group(
        //     function(){
        //         Route::get('list-category',[CategoryController::class,'index'])->name('admin.categorys.listcategory');
        //         Route::get('add-category',[CategoryController::class,'addCategory'])->name('admin.categorys.addCategory');
        //     }
        // );

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


        Route::prefix('users')->name('users.')->controller(UserContronler::class)->group(
            function () {
                Route::get('/', 'listAdmin')->name('list');
            }
        );

        Route::prefix('mails')->name('mails.')->controller(MailController::class)->group(
            function () {
                Route::get('/contact', 'sendMail')->name('sendMail');
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
        Route::prefix('imports')->controller(ImportController::class)->name('imports.')->group(
            function () {
                Route::get('add', 'getFormAdd')->name('getFormAdd');
                Route::get('detail/{id}', 'detail')->name('detail');

                Route::get('edit/{id}', 'getFormEdit')->name('getFormEdit');
                Route::get('/', 'list')->name('list');
                // Route::get('lockOrActive/{id}', 'lockOrActive')->name('lockOrActive');
                Route::post('add', 'add')->name('add');
                Route::post('edit/{id}', 'edit')->name('edit');
            }

        );
    }
);
Route::prefix('client')->name('client.')->group(
    function () {
        Route::prefix('users')->name('user.')->group(
            function () { }
        );
    }
);
    // Attributes Routes
