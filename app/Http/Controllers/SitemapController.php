<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Site;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $entries = [];

        $entries[] = ['alternates' => Site::routeAlternates('home'), 'lastmod' => null];

        // Only published AND indexable pages: a draft / utility page never enters the sitemap.
        foreach (Page::indexable()->get() as $page) {
            $entries[] = ['alternates' => $page->alternates(), 'lastmod' => $page->updated_at?->toDateString()];
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
