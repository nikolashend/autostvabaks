<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects plain-HTTP requests to HTTPS whenever APP_URL itself is https://.
 * Session cookies are "secure", so a page opened over http:// would never keep
 * its session (Livewire then answers 419 "page expired").
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->secure() && str_starts_with((string) config('app.url'), 'https://')) {
            return redirect()->to('https://'.$request->getHttpHost().$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
