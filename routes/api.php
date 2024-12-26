<?php

use App\Http\Controllers\Api\AddressController;
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
