<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** The admin panel is used in Russian regardless of the public site's default language. */
class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('ru');

        return $next($request);
    }
}
