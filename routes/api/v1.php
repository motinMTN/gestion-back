<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/***************** Rutas públicas ******************/

// Login, Register
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::post('/send-reset-password', [LoginController::class, 'SendResetPassword']);
// Password Reset
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [ResetPasswordController::class, 'reset']);
// Test
    Route::get('/test', function () {
        return response()->json(['message' => 'API v1 is working']);
    });

/****************** Rutas protegidas por el middleware 'auth:api' ******************/
Route::middleware('auth:api')->group(function () {
    Route::get('/test-middleware', function () {
        return response()->json(['message' => 'API v1 middleware is working']);
    });
});
