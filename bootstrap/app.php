<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware global
        $middleware->api(\Illuminate\Routing\Middleware\SubstituteBindings::class);
        
        // Middleware de groupe API
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1', // 60 requêtes par minute
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Gestion des exceptions
    })->create();
