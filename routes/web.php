<?php

use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ShopController;
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


Route::get('/', function () {
    return view('admin.index');
});


Route::get('/users', function () {
    return view('admin.user.demoDataTable');
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

            Route::get('reset-password/{id}/{token}', 'getfromResetPassword')->name('getfromResetPassword');
            Route::post('reset-password', 'resetPassword')->name('resetPassword');
            Route::get('getProfileAdmin', 'getProfileAdmin')->name('getProfileAdmin');
        });
    }
);




Route::prefix('admin')->name('admin.')->middleware(['role:admin,employee'])->group(

    function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
        });
        Route::prefix('export')->name('export.')->controller(TemplateExportController::class)->group(
            function () {
                Route::get('/export-sample-file', 'exportSamplefile')->name('exportSamplefile');

                // Route::get('product', [ProductController::class, 'exportProduct'])->name('product');
                // Route::get('supplier', [SupplierController::class, 'exportSupplier'])->name('supplier');
                // Route::get('user', [UserContronler::class, 'exportUser'])->name('user');
                // Route::get('import', [ImportController::class, 'exportImport'])->name('import');

            }
        );



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

        Route::prefix('products')
            ->controller(ProductController::class)
            ->name('products.')->group(function () {
                Route::get('/create',  'create')->name('create'); // Hiển thị form thêm sản phẩm
                Route::post('/store',  'store')->name('store');
                Route::get('/',  'list')->name('list');
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
        Route::prefix('product_variants')->name('product_variants.')->controller(ProductVariantController::class)->group(
            function () {
                Route::get('/{id}', 'list')->name('list');
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
        Route::prefix('imports')->controller(ImportController::class)->name('imports.')->group(
            function () {
                Route::get('add', 'getFormAdd')->name('getFormAdd');
                Route::get('detail/{id}', 'detail')->name('detail');
                Route::post('/upload', 'importExcel')->name('upload');

                Route::get('edit/{id}', 'getFormEdit')->name('getFormEdit');
                Route::get('/list-approved', 'listApproved')->name('listApproved');
                Route::get('/list-pending', 'listPending')->name('listPending');
                Route::get('/list-rejected', 'listRejected')->name('listRejected');

                // Route::get('lockOrActive/{id}', 'lockOrActive')->name('lockOrActive');
                Route::post('add', 'add')->name('add');
                Route::post('edit/{id}', 'edit')->name('edit');
                Route::get('accept/{id}', 'accept')->name('accept')->middleware('role:admin');
                Route::get('reject/{id}', 'reject')->name('reject')->middleware('role:admin');;
                Route::get('list-audit', 'listAudit')->name('listAudit');
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
    }
);
Route::prefix('client')->name('client.')->group(
    function () {
        Route::prefix('users')->name('user.')->group(
            function () {}
        );
        Route::controller(ShopController::class)->group(function () {
            Route::get('shop', 'shop')->name('shop');
            Route::get('/shop/filter', [ShopController::class, 'filter'])->name('filter');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('/orders', [OrderController::class, 'trackOrders'])->name('orders.track');
            Route::post('/orders/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
            Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');

        });



    }
);
