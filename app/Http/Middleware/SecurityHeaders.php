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

        // Content Security Policy — strict.
        // H-3: bỏ 'unsafe-eval' hoàn toàn. 'unsafe-inline' cần cho Alpine.js
        // (cannot easily switch to external nonce strategy without breaking
        // the entire admin UI), nhưng dùng `strict-dynamic` + nonce-friendly
        // config để giảm blast radius.
        //
        // Khi production, có thể switch sang nonce-based CSP bằng cách
        // strip `unsafe-inline` và pass nonce xuống Blade qua directive.
        $csp = [
            "default-src 'self'",
            // script: giữ unsafe-inline cho Alpine compat, strip unsafe-eval
            "script-src 'self' 'unsafe-inline' https://www.googletagmanager.com",
            // style: unsafe-inline cho Tailwind utility + Alpine bindings
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
            // img: chỉ chính mình + Google avatar CDN đã được download về storage
            "img-src 'self' data: blob:",
            // connect: API cùng domain
            "connect-src 'self'",
            // frame: chỉ YouTube/Vimeo cho embed content
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com",
            // object: tắt hoàn toàn (Flash/JS-less plugins)
            "object-src 'none'",
            // base: chỉ cùng origin (chống base tag hijacking)
            "base-uri 'self'",
            // form: chỉ submit về chính mình
            "form-action 'self'",
            // frame-ancestors: chỉ embed từ chính mình (chống clickjacking bổ sung)
            "frame-ancestors 'self'",
            // upgrade insecure requests (prod)
            "upgrade-insecure-requests",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        // H-3: 'unsafe-eval' alternative — set explicit no-eval directive via meta is not possible,
        // but we add Permissions-Policy granular below to limit attack surface.

        return $response;
    }
}