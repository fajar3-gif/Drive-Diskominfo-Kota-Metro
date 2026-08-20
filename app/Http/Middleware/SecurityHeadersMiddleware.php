<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request dan tambahkan security headers
     * untuk mengatasi temuan ZAP scan:
     * - Content Security Policy (CSP)
     * - X-Frame-Options (Anti-Clickjacking)
     * - X-Content-Type-Options (nosniff)
     * - Hapus header X-Powered-By
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Content Security Policy (CSP)
        // Izinkan resource dari origin sendiri; CDN Google Fonts diizinkan khusus untuk font.
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://www.gstatic.com https://apis.google.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: https: blob:; " .
            "connect-src 'self' https://identitytoolkit.googleapis.com https://securetoken.googleapis.com; " .
            "frame-ancestors 'none';"
        );

        // 2. Anti-Clickjacking: X-Frame-Options
        $response->headers->set('X-Frame-Options', 'DENY');

        // 3. X-Content-Type-Options: nosniff
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 4. Referrer-Policy (bonus hardening)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Hapus header X-Powered-By yang membocorkan info server
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
