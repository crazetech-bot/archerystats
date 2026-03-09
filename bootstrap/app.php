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
            'role'            => \App\Http\Middleware\RoleMiddleware::class,
            'identify.tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'root.domain'     => \App\Http\Middleware\RootDomainOnly::class,
        ]);

        // Run tenant identification on every web request
        $middleware->web(append: [
            \App\Http\Middleware\IdentifyTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
