<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Service routes
|--------------------------------------------------------------------------
*/
Route::get('sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Lead photos are private – only a signed-in admin can open them.
Route::get('admin/lead-photo/{lead}/{index}', [LeadController::class, 'photo'])
    ->whereNumber(['lead', 'index'])
    ->middleware('auth')
    ->name('admin.lead-photo');

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
| Every public route exists twice: without a prefix for Estonian (default)
| and as an "l."-named twin under /ru and /en. Use App\Support\Site::route()
| to build links – it picks the right twin for the current language.
|
| New content / SEO pages are created in the admin panel (Страницы) and are
| served by the {slug} route, so no code changes are needed to add them.
*/
$publicRoutes = function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::post('paring', [LeadController::class, 'store'])
        ->middleware('throttle:leads')
        ->name('leads.store');

    Route::get('{slug}', [PageController::class, 'show'])
        ->where('slug', '(?!admin$|livewire$|storage$|up$)[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('page');
};

Route::middleware('locale')
    ->prefix('{locale}')
    ->where(['locale' => 'ru|en'])
    ->name('l.')
    ->group($publicRoutes);

Route::middleware('locale')->group($publicRoutes);
