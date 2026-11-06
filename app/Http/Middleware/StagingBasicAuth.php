<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protects the whole site with HTTP Basic Auth while STAGING_AUTH="user:password"
 * is set in .env (test domain only; leave it empty in production).
 */
class StagingBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('autostvabaks.staging_auth');

        if ($expected === '' || ! str_contains($expected, ':')) {
            return $next($request);
        }

        [$user, $password] = explode(':', $expected, 2);

        if (hash_equals($user, (string) $request->getUser()) && hash_equals($password, (string) $request->getPassword())) {
            return $next($request);
        }

        return response('Authentication required.', 401, [
            'WWW-Authenticate' => 'Basic realm="Autostvabaks test site", charset="UTF-8"',
            'Cache-Control' => 'no-store',
        ]);
    }
}
