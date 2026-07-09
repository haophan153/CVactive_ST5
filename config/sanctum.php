<?php

use Laravel\Sanctum\Sanctum;

return [
    /*
    | M-10: chỉ định stateful domains cụ thể thay vì 'localhost' mặc định
    | — chặn attacker exploit SPA auth ride.
    */
    'stateful' => explode(',', (string) env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1,cvactive.test')),

    /*
    | Sanctum tokens expire sau 30 ngày, user phải re-login.
    | Trước đây tokens persistent forever — nếu bị lộ → mãi mãi đăng nhập được.
    */
    'expiration' => 60 * 24 * 30,

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies'      => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token'  => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],
];
