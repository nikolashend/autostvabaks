<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Site;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $locales = array_keys(Site::locales());
        $entries = [];

        $home = [];
        foreach ($locales as $locale) {
            $home[$locale] = Site::route('home', [], $locale);
        }
        $entries[] = ['alternates' => $home, 'lastmod' => null];

        foreach (Page::active()->get() as $page) {
            $urls = [];
            foreach ($locales as $locale) {
                $urls[$locale] = $page->url($locale);
            }
            $entries[] = ['alternates' => $urls, 'lastmod' => $page->updated_at?->toDateString()];
        }

        return response()
            ->view('site.sitemap', ['entries' => $entries, 'defaultLocale' => Site::defaultLocale()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = config('autostvabaks.noindex')
            ? ['User-agent: *', 'Disallow: /']
            : ['User-agent: *', 'Disallow: /admin', '', 'Sitemap: '.route('sitemap')];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
