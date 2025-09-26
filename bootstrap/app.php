<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware nhóm web
        $middleware->web([
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Đăng ký alias 'locale'
        $middleware->alias([
            'locale' => \App\Http\Middleware\SetLocale::class,
            'admin'  => \App\Http\Middleware\AuthenticateMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        App\Providers\AuthServiceProvider::class, // 👈 thêm dòng này
    ])->create();
