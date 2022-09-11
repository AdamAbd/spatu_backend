<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {

        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/verify', [AuthController::class, 'verify'])->name('auth.verify');
        Route::post('/resend_code', [AuthController::class, 'resendCode'])->name('auth.resendCode');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    });

    Route::group(['middleware' => ['auth:sanctum', 'ability:user|accessToken']], function () {

        Route::group(['prefix' => 'user'], function () {

            Route::get('/detail', [UserController::class, 'detail'])->name('user.detail');
        });
    });

    Route::group(['middleware' => ['auth:sanctum', 'ability:user|refreshToken']], function () {

        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh-token', [RefreshTokenController::class, 'refreshToken'])->name('auth.refreshToken');
    });
});
