<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
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
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
