<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
       // dd(dirname(__DIR__));
        // $app = new Illuminate\Foundation\Application(
        //     $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
        // );
//         $app = new Illuminate\Foundation\Application(dirname(__DIR__));
//         $app->middleware([
//     \App\Http\Middleware\TrustProxies::class,
//     \Illuminate\Http\Middleware\HandleCors::class,
//     \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
//     \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
//     \App\Http\Middleware\TrimStrings::class,
//     \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
// ]);


//         // Register route middleware groups (like 'api' or 'web')
//         $app->router->middlewareGroup('api', [
//             \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
//             'throttle:api',
//             \Illuminate\Routing\Middleware\SubstituteBindings::class,
//         ]);
        // Global middleware
        $middleware->append([
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // Middleware groups (like 'api')
        $middleware->group('api', [
           // EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        \App\Providers\RateLimiterServiceProvider::class,
    ])

    ->create();

    