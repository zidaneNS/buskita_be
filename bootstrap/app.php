<?php

use App\Http\Middleware\AdminCoMiddleware;
use App\Http\Middleware\CoLeaderOnly;
use App\Http\Middleware\RefreshScheduleMiddleware;
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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'co-co_leader' => AdminCoMiddleware::class,
            'co_leader' => CoLeaderOnly::class
        ])->append([
            RefreshScheduleMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
