<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require base_path('routes/api/v1.php');  // Rutas de la versión 1
});

Route::prefix('v2')->group(function () {
    require base_path('routes/api/v2.php');  // Rutas de la versión 2
});
