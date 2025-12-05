<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Load AI routes
            require __DIR__.'/../routes/ai.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'ai.limit' => \App\Http\Middleware\AiRateLimitMiddleware::class,
        ]);
        
        // Add SetLocale to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        
        // Ensure CSRF protection is enabled
        $middleware->validateCsrfTokens(except: [
            // Add any routes you want to exclude from CSRF verification
        ]);
    })
    ->booting(function () {
        // Configure AI rate limiter
        RateLimiter::for('ai', function (Request $request) {
            return Limit::perMinute(config('ai.rate_limit.max_requests', 20))
                ->by($request->user()?->id ?: $request->ip());
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

