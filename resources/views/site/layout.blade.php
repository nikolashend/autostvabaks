@php
    use App\Support\Site;

    $isHome = $isHome ?? false;
    $alternates = $alternates ?? [];
    $metaTitle = $metaTitle ?? config('app.name');
    $metaDescription = $metaDescription ?? __('site.meta.description');
    $canonical = $alternates[$currentLocale] ?? url()->current();
    $ogImage = asset('assets/imgs/hero/hero.jpg');
    // Swiper only serves the reviews carousel and only when there are more reviews than fit in the grid.
    $needsSwiper = isset($testimonials) && $testimonials->count() > 3;
@endphp
<!doctype html>
<html lang="{{ $currentLocale }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="theme-color" content="{{ config('autostvabaks.brand.ink') }}">
    @if (! empty($noindex) || config('autostvabaks.noindex'))<meta name="robots" content="noindex, nofollow">@endif

    <link rel="canonical" href="{{ $canonical }}">
    @foreach ($alternates as $code => $href)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
    @endforeach
    @isset($alternates[Site::defaultLocale()])
        <link rel="alternate" hreflang="x-default" href="{{ $alternates[Site::defaultLocale()] }}">
    @endisset

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="{{ $locales[$currentLocale]['og'] ?? 'et_EE' }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/svg+xml" href="{{ Site::asset('assets/imgs/brand/favicon.svg') }}">

    {{-- Fonts are self-hosted (public/assets/fonts); the Latin subset is used by every page --}}
    {{-- same URL as the @font-face src (no cache-busting query), otherwise the font is fetched twice --}}
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('assets/fonts/urbanist-latin.woff2') }}" crossorigin>

    {{-- Bootstrap (grid, modal, collapse, dropdown) + the Autostvabaks design system --}}
    <link href="{{ Site::asset('assets/css/vendors/bootstrap.min.css') }}" rel="stylesheet">
    @if ($needsSwiper)
        <link href="{{ Site::asset('assets/css/plugins/swiper-bundle.min.css') }}" rel="stylesheet">
    @endif
    <link href="{{ Site::asset('assets/css/autostvabaks.css') }}" rel="stylesheet">

    @stack('head')

    {{-- Analytics / Google Ads tags: admin panel → Настройки --}}
    {!! $settings['head_scripts'] ?? '' !!}
</head>
<body class="{{ $isHome ? 'asv-home' : 'asv-inner' }}">
    <a class="asv-skip" href="#main">{{ __('site.nav.home') }}</a>

    @include('site.partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('site.partials.footer')
    @include('site.partials.quick-contact')
    @if ($isHome)
        @include('site.partials.sticky-cta')
    @endif

    @stack('modals')

    <script src="{{ Site::asset('assets/js/bootstrap.bundle.min.js') }}" defer></script>
    @if ($needsSwiper)
        <script src="{{ Site::asset('assets/js/swiper-bundle.min.js') }}" defer></script>
    @endif
    @stack('scripts')
    <script src="{{ Site::asset('assets/js/autostvabaks.js') }}" defer></script>
</body>
</html>
