<?php

namespace App\Http\Middleware;

use App\Support\Csp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Conservative security headers for every response, plus the X-Robots-Tag
 * twin of the <meta name="robots"> tag on the test domain (SITE_NOINDEX) and
 * on everything under /admin.
 *
 * Content-Security-Policy (ADR-24): public HTML pages get a nonce-based policy
 * (report-only by default, CSP_MODE=enforce once the report-only run is clean).
 * /admin and /livewire are excluded – Filament needs inline/eval.
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

        $admin = $request->is('admin', 'admin/*', 'livewire/*');

        if (config('autostvabaks.noindex') || $admin) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        if (! $admin && ($header = Csp::headerName()) && str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            $response->headers->set($header, Csp::policy(Csp::nonce()));
        }

        return $response;
    }
}
