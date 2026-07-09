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
        // Security headers cho mọi response web (chống XSS, clickjacking, MIME sniffing)
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'hr' => \App\Http\Middleware\HRMiddleware::class,
        ]);

        // Bỏ CSRF cho IPN callbacks từ cổng thanh toán (chỉ gateway gọi, có verify signature)
        $middleware->validateCsrfTokens(except: [
            'payment/vnpay/ipn',
            'payment/momo/ipn',
        ]);

        // Rate limit cho các public endpoints dễ bị spam/brute-force
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
