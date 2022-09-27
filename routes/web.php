<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/halo', function () {
    return response()->json('Halo Dunia');
});

Route::prefix('v1')->group(function () {

    Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {

        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/verify', [AuthController::class, 'verify'])->name('auth.verify');
        Route::post('/resend_code', [AuthController::class, 'resendCode'])->name('auth.resendCode');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/google', [AuthController::class, 'google'])->name('auth.google');
        Route::put('/reset', [AuthController::class, 'reset'])->name('auth.reset');
    });

    Route::group(['middleware' => ['auth:sanctum', 'ability:user|accessToken']], function () {

        Route::group(['prefix' => 'user'], function () {

            Route::get('/detail', [UserController::class, 'detail'])->name('user.detail');
            Route::put('/update', [UserController::class, 'update'])->name('user.update');
        });
    });

    Route::group(['middleware' => ['auth:sanctum', 'ability:user|refreshToken']], function () {

        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh-token', [RefreshTokenController::class, 'refreshToken'])->name('auth.refreshToken');
    });
});
