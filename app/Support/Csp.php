<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Content-Security-Policy for the public site (ADR-24).
 *
 * One nonce per request; every <script> the layout emits carries it, including
 * the GTM bootstrap and whatever the admin pastes into Settings → head scripts.
 * The policy uses 'strict-dynamic', so scripts loaded by nonced scripts (GTM →
 * gtag.js) are allowed without listing every vendor host. Filament/Livewire
 * under /admin are excluded – they rely on inline/eval and keep the Stage-1
 * headers only.
 */
class Csp
{
    public static function mode(): string
    {
        $mode = strtolower((string) config('autostvabaks.csp', 'report-only'));

        return in_array($mode, ['off', 'report-only', 'enforce'], true) ? $mode : 'report-only';
    }

    public static function nonce(): string
    {
        $request = request();

        if (! $request->attributes->has('csp_nonce')) {
            $request->attributes->set('csp_nonce', Str::random(24));
        }

        return (string) $request->attributes->get('csp_nonce');
    }

    public static function headerName(): ?string
    {
        return match (static::mode()) {
            'enforce' => 'Content-Security-Policy',
            'report-only' => 'Content-Security-Policy-Report-Only',
            default => null,
        };
    }

    /** Policy for public HTML responses. */
    public static function policy(string $nonce): string
    {
        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            // own scripts + GTM bootstrap carry the nonce; GTM-loaded tags are allowed via strict-dynamic
            "script-src 'self' 'nonce-{$nonce}' 'strict-dynamic' https://www.googletagmanager.com",
            // Bootstrap/Swiper set element styles from JS; no external stylesheet hosts (fonts are self-hosted)
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: https:",
            "font-src 'self'",
            "connect-src 'self' https://www.googletagmanager.com https://*.google-analytics.com https://*.analytics.google.com https://stats.g.doubleclick.net https://www.google.com",
            // click-to-load Google Maps embed; GTM preview/debug iframe
            'frame-src https://www.google.com https://www.googletagmanager.com',
        ];

        // browsers ignore (and log) this directive in report-only mode
        if (static::mode() === 'enforce') {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }

    /** Adds the request nonce to every <script> tag of an admin-authored snippet. */
    public static function withNonce(?string $html): string
    {
        $html = (string) $html;

        if (trim($html) === '') {
            return '';
        }

        $nonce = static::nonce();

        return preg_replace_callback('/<script\b([^>]*)>/i', function (array $m) use ($nonce) {
            $attrs = preg_replace('/\s+nonce\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $m[1]);

            return '<script nonce="'.$nonce.'"'.$attrs.'>';
        }, $html);
    }
}
