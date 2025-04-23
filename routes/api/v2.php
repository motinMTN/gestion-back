<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V2
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/***************** Rutas públicas ******************/
Route::get('/test', function () {
    return response()->json(['message' => 'API v2 is working']);
});

/****************** Rutas protegidas por el middleware 'auth:api' ******************/
Route::middleware('auth:api')->group(function () {
    Route::get('/test-middleware', function () {
        return response()->json(['message' => 'API v2 middleware is working']);
    });
});

