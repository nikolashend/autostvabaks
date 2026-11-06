<?php

namespace App\Http\Middleware;

use App\Support\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the application language from the URL: "/" → et, "/ru" → ru, "/en" → en.
 * The {locale} route parameter is consumed here so controllers do not receive it.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        app()->setLocale(Site::isLocale($locale) ? $locale : Site::defaultLocale());

        $request->route()?->forgetParameter('locale');

        return $next($request);
    }
}
