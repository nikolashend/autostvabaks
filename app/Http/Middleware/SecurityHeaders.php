<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Conservative security headers for every response, plus the X-Robots-Tag
 * twin of the <meta name="robots"> tag on the test domain (SITE_NOINDEX) and
 * on everything under /admin. No CSP yet: Filament/Livewire and the Google
 * Maps embed would need an audited policy first (Stage 2+).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), payment=(), usb=()');

        if (config('autostvabaks.noindex') || $request->is('admin', 'admin/*', 'livewire/*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
