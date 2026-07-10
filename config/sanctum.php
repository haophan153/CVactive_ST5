<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from these domains are treated as stateful (cookie-based auth).
    | SECURITY (fix #3): The previous missing config allowed ANY origin to send
    | cookie auth. We now restrict to explicit domains.
    |
    | This is required for the Sanctum API to maintain a logged-in session
    | with your front-end via cookies. Typically, this would be your
    | production and local domains.
    |
    */

    'stateful' => explode(',', (string) env(
        'SANCTUM_STATEFUL_DOMAINS',
        env('APP_ENV') === 'production'
            ? 'cvactive.com,www.cvactive.com'
            : 'localhost,localhost:8000,127.0.0.1,127.0.0.1:8000'
    )),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Sanctum provides two authentication guards: `web` and `sanctum`. The
    | `web` guard uses the standard Laravel session cookie, while `sanctum`
    | guard uses Sanctum's token-based authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | SECURITY (fix #17): Tokens now expire after 8 hours to limit the
    | window of opportunity for token reuse after theft.
    |
    */

    'expiration' => (int) env('SANCTUM_EXPIRATION', 480), // 8 hours

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | SECURITY (fix #17): Adding a version prefix helps identify leaked tokens
    | and rotate compromised generations quickly.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'cvk_'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating with Sanctum, the API request will typically
    | contain an Authorization header with the access token issued to the
    | user. Sanctum also supports stateful API authentication via cookies,
    | for SPA front-ends.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies'      => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token'  => Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ],
];
