<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // $schedule->command('temp:clear')->daily(); // ✅ Run daily at midnight
        // $schedule->command('temp:clear')->everyMinute(); // ✅ Run daily at midnight
        // $schedule->command('queue:work --stop-when-empty')->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Define exception handlers if needed
    })
    ->create();

// ✅ Register custom route service provider here
$app->register(App\Providers\RouteServiceProvider::class);

return $app;
