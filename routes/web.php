<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GoogleController;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\MailController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserContronler;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
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
