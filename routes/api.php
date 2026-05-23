<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientOrderController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

Route::prefix('Auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/customer/login', [AuthController::class, 'customerLogin']);
    Route::post('/customer/register', [AuthController::class, 'customerRegister']);

    Route::middleware(['jwt.auth', 'jwt.token_version'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('role:Admin,Staff');
    });
});


Route::get('/Categories', [CategoryController::class, 'index']);

Route::get('/Product', [ProductController::class, 'index']);
Route::get('/Product/search-elastic', [ProductController::class, 'searchElastic']);
Route::get('/Product/{id}', [ProductController::class, 'show'])->whereNumber('id');

Route::middleware(['jwt.auth', 'jwt.token_version', 'role:Admin,Staff'])->group(function () {
    Route::post('/Product', [ProductController::class, 'store']);
    Route::put('/Product/{id}', [ProductController::class, 'update'])->whereNumber('id');
    Route::delete('/Product/{id}', [ProductController::class, 'destroy'])->whereNumber('id');
});


Route::middleware(['jwt.auth', 'jwt.token_version', 'role:Customer'])->prefix('client')->group(function () {
    Route::post('/orders', [ClientOrderController::class, 'store']);
    Route::get('/orders', [ClientOrderController::class, 'index']);
    Route::get('/orders/{id}', [ClientOrderController::class, 'show'])->whereNumber('id');
});

Route::middleware(['jwt.auth', 'jwt.token_version', 'role:Admin,Staff'])->group(function () {
    Route::post('/Orders', [OrderController::class, 'store']);
    Route::get('/Orders', [OrderController::class, 'index']);
    Route::get('/Orders/{id}', [OrderController::class, 'show'])->whereNumber('id');
    Route::put('/Orders/{id}', [OrderController::class, 'update'])->whereNumber('id');
    Route::put('/Orders/{id}/status', [OrderController::class, 'updateStatus'])->whereNumber('id');
    Route::delete('/Orders/{id}', [OrderController::class, 'destroy'])->whereNumber('id');
});
