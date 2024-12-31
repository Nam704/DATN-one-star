<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('admin')->group(
    function () {
        Route::prefix('users')->name('user.')->group(
            function () { }
        );
        Route::prefix('imports')->controller(ImportController::class)->group(
            function () {
                // Route::get('provinces', 'getProvinces');
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
