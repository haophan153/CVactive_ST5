<?php

/*
| L-8: CORS config — chỉ cho phép domain chính chứ không phải wildcard.
| Trước đây: Access-Control-Allow-Origin: * mặc định nếu không config
| → bất kỳ domain nào cũng có thể gọi API kèm credentials.
*/
return [
    'paths' => ['api/*', 'oauth/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://localhost:8000')))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-XSRF-TOKEN',
        'X-CSRF-TOKEN',
        'X-Requested-With',
        'Origin',
    ],

    'exposed_headers' => [
        'X-RateLimit-Remaining',
        'X-RateLimit-Limit',
    ],

    'max_age' => 86400,

    'supports_credentials' => true,
];
