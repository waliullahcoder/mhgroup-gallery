<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Optional: bind services here
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin routes
        Route::middleware(['web'])
            ->prefix('admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
    }
}
