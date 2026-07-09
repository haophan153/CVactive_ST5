<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thêm các security headers để chống XSS, clickjacking, MIME sniffing, downgrade attack.
 *
 * Áp dụng cho mọi response HTML (route web).
 * Riêng route admin nên ép thêm CSP strict.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!env('SECURITY_HEADERS_ENABLED', true)) {
            return $next($request);
        }

        $response = $next($request);

        // Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS filter (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions (tắt các API không dùng)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HTTPS (1 năm, include subdomains)
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content Security Policy — cho phép inline scripts của Blade + Alpine.js + Google fonts
        // Lưu ý: nếu thêm external scripts, phải thêm domain vào script-src
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
            "img-src 'self' data: blob: https:",
            "connect-src 'self'",
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}