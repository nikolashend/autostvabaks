<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * One canonical origin. When APP_URL is https://…, every request that arrives
 * over plain HTTP or on another host (www., an old staging alias, the bare
 * server IP) is 301-redirected to APP_URL's scheme + host with the same path.
 *
 * Session cookies are "secure", so a page opened over http:// would never keep
 * its session (Livewire then answers 419 "page expired") – hence the redirect
 * is not only an SEO concern.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = (string) config('app.url');

        if (! str_starts_with($appUrl, 'https://')) {
            return $next($request);
        }

        $canonicalHost = parse_url($appUrl, PHP_URL_HOST);
        $requestHost = $request->getHost();

        $wrongHost = $canonicalHost && $requestHost !== $canonicalHost
            && ! in_array($requestHost, ['localhost', '127.0.0.1'], true);

        if (! $request->secure() || $wrongHost) {
            return redirect()->to('https://'.$canonicalHost.$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
