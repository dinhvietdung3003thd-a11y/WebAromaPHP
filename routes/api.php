<?php

use App\Http\Controllers\Api\AuthController;
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
