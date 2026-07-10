<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SECURITY (fix #10): Production-only middleware that 301-redirects HTTP → HTTPS.
 *
 * Hooks into TrustProxies when behind a load balancer / Cloudflare so the check
 * is made against the original protocol, not the proxy connection.
 *
 * This is a defense-in-depth in case the web server (nginx / apache) is not
 * configured to issue its own redirect, or in case `isSecure()` returns false
 * because of incorrect proxy header forwarding.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (env('APP_ENV') === 'production' && ! $request->isSecure()) {
            // Trust X-Forwarded-Proto from a configured proxy.
            $forwardedProto = $request->header('X-Forwarded-Proto');
            if ($forwardedProto !== 'https' && ! $request->isSecure()) {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }
}
