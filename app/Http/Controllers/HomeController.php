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
        return view('site.home', [
            'testimonials' => Testimonial::published()->get(),
            'faqs' => Faq::active()->get(),
            'termsPage' => Page::byKey('terms'),
            'alternates' => Site::routeAlternates('home'),
            'metaTitle' => __('site.meta.title'),
            'metaDescription' => __('site.meta.description'),
        ]);
    }
}
