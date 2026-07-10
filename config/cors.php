<?php

/*
|--------------------------------------------------------------------------
| CORS (Cross-Origin Resource Sharing) Configuration
|--------------------------------------------------------------------------
|
| Define allowed origins, methods, and headers for cross-domain API access.
| SECURITY (fix #3): Removed implicit `*` allowance — the missing file
| defaulted to allowing every origin including untrusted evil.com. We now
| restrict to the production + local dev domains only, and require explicit
| origin matching.
|
*/

$allowedOrigins = array_values(array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', '')))));

if (empty($allowedOrigins)) {
    $allowedOrigins = match (env('APP_ENV')) {
        'production' => [
            'https://cvactive.com',
            'https://www.cvactive.com',
        ],
        default => [
            'http://localhost:8000',
            'http://127.0.0.1:8000',
            'http://localhost:5173', // Vite dev server
            'http://127.0.0.1:5173',
        ],
    };
}

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'X-CSRF-TOKEN',
        'X-Requested-With',
        'Authorization',
        'X-XSRF-TOKEN',
        'Accept',
        'Origin',
    ],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
    ],

    'max_age' => 86400,

    'supports_credentials' => true,
];
