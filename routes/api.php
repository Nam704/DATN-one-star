<?php


use App\Http\Controllers\Api\AttributeController;

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\ImportDetailController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductImageDescriptionController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(
    function () {
        Route::prefix('attributes')->controller(AttributeController::class)->group(function () {
            Route::get('get-all', 'getAll');
            Route::get('get-by-id/{id}', 'getAttributeById');
            Route::post('creat-values/{id}', 'createValue');
            // Route::post('update/{id}', 'update');
            // Route::delete('delete/{id}', 'delete');

        });
        Route::prefix('brands')->controller(BrandController::class)->group(function () {
            Route::post('add', 'store');
        });

        Route::prefix('users')->name('user.')->group(
            function () { }
        );
        Route::prefix('categories')->controller(CategoryController::class)->group(
            function () {

                Route::post('add', 'store');
            }
        );
        Route::prefix('product-images-description')
            ->controller(ProductImageDescriptionController::class)
            ->group(
                function () {
                    Route::post('/upload', 'uploadImage'); // Upload ảnh
                    Route::delete('/{id}', 'deleteImage'); // Xóa ảnh
                    Route::post('/update-description',  'updateDescription'); // Cập nhật nội dung mô tả
                }
            );
        Route::prefix('notifications')->controller(NotificationController::class)->name('notifications.')->group(
            function () {
                Route::get('/unread/{userId}', 'getUnreadCount');
                Route::post('/mark-read/{id}',  'markAsRead');
            }
        );
        Route::prefix('imports')->controller(ImportController::class)->group(
            function () {
                Route::post('accept-all', 'acceptAll')->name('acceptAll');
                Route::post('reject-all', 'rejectAll')->name('rejectAll');
                // Route::get('provinces', 'getProvinces');
                Route::get('{id}/details', [ImportController::class, 'getImportDetails']);
                Route::post('/confirm-import', [ImportController::class, 'confirmImport'])->name('import.confirm');
            }
        );
    }
);
Route::prefix('product-variants')->controller(ProductVariantController::class)->group(
    function () {
        Route::get('/{idProduct}', 'getProductVariants');
        Route::get('total/{idProduct}', 'total');
    }
);
Route::prefix('products')->controller(ProductController::class)->group(
    function () {
        Route::get('total', 'total');
        Route::get('list', 'list');
    }
);
Route::prefix('address')->controller(AddressController::class)->name('address.')->group(
    function () {
        Route::get('provinces', 'getProvinces');
        Route::get('districts/{provinceId}',  'getDistrictsByProvince');
        Route::get('wards/{districtId}', 'getWardsByDistrict');
    }
);
Route::prefix('client')->group(
    function () {
        Route::prefix('users')->name('user.')->group(
            function () { }
        );
    }
);
