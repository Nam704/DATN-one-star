<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GoogleController;
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

        Route::prefix('categorys')->name('categorys')->group(
            function(){
                Route::get('list-category',[CategoryController::class,'index'])->name('listcategory');
                Route::get('add-category',[CategoryController::class,'addCategory'])->name('addcategory');
            }
        );


        Route::prefix('users')->name('user.')->group(
            function () { }
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
