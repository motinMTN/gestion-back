<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ApiRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api/v1')
            ->middleware('api')
            ->group(base_path('routes/api/v1.php'));

        Route::prefix('api/v2')
            ->middleware('api')
            ->group(base_path('routes/api/v2.php'));
    }
}

