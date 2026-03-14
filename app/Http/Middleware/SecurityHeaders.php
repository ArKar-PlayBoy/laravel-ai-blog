<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $cspScripts = "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com";
        $cspStyles = "'self' 'unsafe-inline' https://cdnjs.cloudflare.com";
        $cspConnect = "'self' https:";

        if (app()->environment('local')) {
            $viteDevOrigins = "http://localhost:5173 http://127.0.0.1:5173";
            $viteWsOrigins = "ws://localhost:5173 ws://127.0.0.1:5173";

            $cspScripts .= ' ' . $viteDevOrigins;
            $cspStyles .= ' ' . $viteDevOrigins;
            $cspConnect .= ' ' . $viteDevOrigins . ' ' . $viteWsOrigins;
        }

        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src {$cspScripts}; " .
            "script-src-elem {$cspScripts}; " .
            "style-src {$cspStyles}; " .
            "style-src-elem {$cspStyles}; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' https://cdnjs.cloudflare.com; " .
            "connect-src {$cspConnect}; " .
            "frame-ancestors 'self';"
        );

        return $response;
    }
}
