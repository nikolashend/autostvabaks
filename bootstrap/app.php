<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'locale' => \App\Http\Middleware\SetLocale::class,
        ]);

        // Whole-site HTTP Basic Auth on the test domain (STAGING_AUTH in .env).
        $middleware->prepend(\App\Http\Middleware\StagingBasicAuth::class);

        // The public lead form is anonymous and keeps no session state, so it is
        // protected by a honeypot + rate limit instead of a CSRF token. A token
        // would only break the form for visitors who leave the tab open for
        // hours (expired session → 419) or once pages are served from a cache.
        $middleware->validateCsrfTokens(except: ['paring', '*/paring']);

        // The only login in this project is the admin panel.
        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
