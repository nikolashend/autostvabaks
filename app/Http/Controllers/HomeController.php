<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use App\Support\Site;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $alternates = [];
        foreach (array_keys(Site::locales()) as $locale) {
            $alternates[$locale] = Site::route('home', [], $locale);
        }

        return view('site.home', [
            'testimonials' => Testimonial::active()->get(),
            'faqs' => Faq::active()->get(),
            'termsPage' => Page::byKey('terms'),
            'alternates' => $alternates,
            'metaTitle' => __('site.meta.title'),
            'metaDescription' => __('site.meta.description'),
        ]);
    }
}
