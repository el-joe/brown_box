<?php

use App\Http\Middleware\AdminAuthenticated;
use App\Http\Middleware\AdminAuthorize;
use App\Http\Middleware\AffiliateAuthenticated;
use App\Http\Middleware\CustomerAuthenticated;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/admin.php'));
            Route::middleware('web')->group(base_path('routes/affiliate.php'));
            Route::middleware('web')->group(base_path('routes/website.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuthenticated::class,
            'admin.can' => AdminAuthorize::class,
            'affiliate.auth' => AffiliateAuthenticated::class,
            'customer.auth' => CustomerAuthenticated::class,
            'web.locale' => SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
