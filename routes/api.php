<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductColorTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
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
        Route::post('/google', [AuthController::class, 'google'])->name('auth.google');
        Route::put('/reset', [AuthController::class, 'sendReset'])->name('auth.sendReset');
        Route::put('/reset_password', [AuthController::class, 'resetPassword'])->name('auth.resetPassword');
    });

    Route::group(['middleware' => ['auth:sanctum', 'ability:user|refreshToken']], function () {

        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh_token', [RefreshTokenController::class, 'refreshToken'])->name('auth.refreshToken');
    });

    Route::group(['middleware' => ['auth:sanctum', 'ability:user|accessToken']], function () {

        Route::group(['prefix' => 'user'], function () {

            Route::get('/detail', [UserController::class, 'detail'])->name('user.detail');
            Route::put('/update', [UserController::class, 'update'])->name('user.update');
        });
    });

    Route::get('/product', [ProductController::class, 'index'])->name('product.index');

    Route::group(['middleware' => ['auth:sanctum', 'ability:user|accessToken']], function () {

        Route::apiResource('brand', BrandController::class);
        Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::post('/product', [ProductController::class, 'store'])->name('product.store');
        Route::apiResource('/product_color_type', ProductColorTypeController::class);
        Route::apiResource('/product_size', ProductSizeController::class);
    });
});
