<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Site;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::findBySlug($slug, app()->getLocale());

        abort_if($page === null, 404);

        $alternates = [];
        foreach (array_keys(Site::locales()) as $locale) {
            $alternates[$locale] = $page->url($locale);
        }

        return view('site.page', [
            'page' => $page,
            'alternates' => $alternates,
            'metaTitle' => $page->t('title').' – '.config('app.name'),
            'metaDescription' => $page->t('meta_description')
                ?: Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($page->t('content')))), 155),
        ]);
    }
}
