<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * M-7 + M-9: Bảo vệ static files trong /storage khỏi bị
 * sniff thành application/x-httpd-php khi attacker upload
 * file với double extension (e.g. shell.php.jpg) — server phải
 * từ chối thực thi bất kể MIME.
 */
class StorageFileMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Chỉ áp dụng cho static storage path
        if ($request->is('storage/*')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Cache-Control', 'public, max-age=86400');

            // Block known executable extensions
            $extension = strtolower($request->route('path')
                ? pathinfo($request->route('path'), PATHINFO_EXTENSION)
                : '');

            $blocked = ['php', 'phtml', 'phps', 'pht', 'shtml',
                        'php3', 'php4', 'php5', 'php7', 'phar',
                        'pl', 'py', 'jsp', 'asp', 'aspx', 'cgi',
                        'sh', 'bash', 'exe', 'bat'];

            if (in_array($extension, $blocked, true)) {
                abort(403);
            }

            // Block request với null byte (path traversal)
            if (str_contains($request->getRequestUri(), "\0")) {
                abort(400);
            }
        }

        return $response;
    }
}
