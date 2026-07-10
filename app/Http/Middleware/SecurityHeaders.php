<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thêm các security headers để chống XSS, clickjacking, MIME sniffing, downgrade attack.
 *
 * SECURITY (fix #4 / #10):
 *  - HSTS forces upgrade 1 year after first visit (preload-ready).
 *  - CSP locks down clickjacking (frame-ancestors 'self'), MIME sniffing
 *    (X-Content-Type-Options), XSS via script-src whitelist, and form-action.
 *  - Required external sources are explicitly enumerated. 'unsafe-eval' is
 *    required because Alpine.js evaluates runtime expressions such as
 *    `x-show="open"` via the JavaScript Function constructor — removing it
 *    silently disables every x-* directive (the dropdown remained permanently
 *    open after the previous audit). CSP3 nonce is the long-term fix; for
 *    this project we accept 'unsafe-eval' in exchange for Alpine.js usage.
 *  - When you remove Alpine.js / migrate to Vue 3, remove 'unsafe-eval' here.
 *
 *  External origins required by the application (kept narrow intentionally):
 *  - fonts.googleapis.com / fonts.gstatic.com      — Google Fonts UI + editor
 *  - fonts.bunny.net                                — Bunny Fonts (privacy-friendlier)
 *  - cdn.jsdelivr.net                               — SortableJS, html2canvas, Trix editor
 *                                                    (CV editor, PNG export, blog editor)
 *  - www.googletagmanager.com                       — GA4 (only if used; safe to leave even
 *                                                    if not configured)
 *
 *  Add NEW domains deliberately. Each entry is a deliberate permission, not a
 *  wildcard — never replace with `*`.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! env('SECURITY_HEADERS_ENABLED', true)) {
            return $next($request);
        }

        $response = $next($request);

        // ─── Clickjacking ──────────────────────────────────────────────
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // ─── MIME sniffing ─────────────────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ─── XSS filter (legacy browsers) ──────────────────────────────
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ─── Referrer ──────────────────────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ─── Disable APIs we never use ─────────────────────────────────
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=()'
        );

        // ─── Cross-Origin isolation hints ─────────────────────────────
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // ─── HSTS — only on HTTPS ──────────────────────────────────────
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // ─── Content Security Policy ──────────────────────────────────
        //
        // Critical: 'unsafe-eval' is intentional. Alpine.js cannot evaluate
        // x-show / x-text / :class bindings without it. After refactoring
        // to Vue 3 (or Livewire 3), this can be removed.
        //
        // Every domain listed here is required by a specific blade — see
        // the doc-comment above for traceability.
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' "
                .'https://cdn.jsdelivr.net '
                .'https://www.googletagmanager.com',
            "style-src 'self' 'unsafe-inline' "
                .'https://fonts.googleapis.com '
                .'https://fonts.bunny.net '
                .'https://cdn.jsdelivr.net',
            "font-src 'self' "
                .'https://fonts.gstatic.com '
                .'https://fonts.bunny.net '
                .'https://cdn.jsdelivr.net '
                .'data:',
            "img-src 'self' data: blob: https:",
            "connect-src 'self'",
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            'upgrade-insecure-requests',
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
