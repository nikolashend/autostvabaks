<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Setting;
use App\Support\Site;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Canonical, hreflang, OG and schema URLs are generated from APP_URL, never
        // from the Host header, so a stray alias (www., server IP, old staging name)
        // can never leak into SEO output. Local dev and tests keep the request host.
        if (! $this->app->runningUnitTests() && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }

        // Lead form: generous for a real visitor, tight enough to stop a flood.
        RateLimiter::for('leads', function (Request $request) {
            return Limit::perMinutes(10, 8)
                ->by($request->ip())
                ->response(fn () => response()->json(['message' => __('site.errors.throttle')], 429));
        });

        // Data every public page needs: contacts, footer links, language switcher.
        // Loaded once per request, shared with the layout, the pages and their partials.
        View::composer(['site.*', 'errors.*'], function ($view) {
            static $shared = null;

            $shared ??= (function () {
                $footerPages = Cache::rememberForever('site.footer_pages', fn () => Page::active()
                    ->where('footer_group', '!=', 'none')
                    ->get());

                return [
                    'settings' => Setting::allValues(),
                    'footerInfoPages' => $footerPages->where('footer_group', 'info'),
                    'footerLegalPages' => $footerPages->where('footer_group', 'legal'),
                    'locales' => Site::locales(),
                ];
            })();

            $view->with($shared + ['currentLocale' => app()->getLocale()]);
        });
    }
}
