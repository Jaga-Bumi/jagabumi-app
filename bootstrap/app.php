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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'org.manager' => \App\Http\Middleware\IsOrgManager::class,
            'has.profile' => \App\Http\Middleware\HasProfile::class,
        ]);
        
        $middleware->redirectGuestsTo('/');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
