<?php


use App\Http\Controllers\API\AttributeController;

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\ImportDetailController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use Illuminate\Http\Request;

use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\Api\NotificationController;
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
Route::prefix('admin')->group(
    function () {
        Route::prefix('users')->name('user.')->group(
            function () { }
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
