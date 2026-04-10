<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ForceJsonAcceptHeader;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/login',
            'api/logout',
            'api/personal',
            'api/personal/*',
            'api/clientes',
            'api/clientes/*',
            'api/pedidos',
            'api/pedidos/*',
            'api/inventario',
            'api/inventario/*',
            'api/sucursales',
            'api/sucursales/*',
        ]);

        $middleware->alias([
            'force.json.accept' => ForceJsonAcceptHeader::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
