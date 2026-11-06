<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Setting;
use App\Support\Site;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
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
