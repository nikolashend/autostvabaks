@php
    use App\Support\Analytics;
    use App\Support\Csp;
    use App\Support\Site;

    $isHome = $isHome ?? false;
    $alternates = $alternates ?? [];
    $metaTitle = $metaTitle ?? config('app.name');
    $metaDescription = $metaDescription ?? __('site.meta.description');
    $canonical = $alternates[$currentLocale] ?? url()->current();
    // Brand social image per locale (public/assets/imgs/brand/og-{locale}.jpg, 1200×630) – never the hero photo
    $ogImage = Site::ogImage($currentLocale);
    // Swiper only serves the reviews carousel and only when there are more reviews than fit in the grid.
    $needsSwiper = isset($testimonials) && $testimonials->count() > 3;
    $nonce = Csp::nonce();
    $gtmId = Analytics::gtmId();
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
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ config('app.name') }} – {{ __('site.hero.eyebrow') }}">
    <meta property="og:locale" content="{{ $locales[$currentLocale]['og'] ?? 'et_EE' }}">
    @foreach ($locales as $code => $locale)
        @if ($code !== $currentLocale)<meta property="og:locale:alternate" content="{{ $locale['og'] }}">@endif
    @endforeach
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

    {{-- Analytics contract (ADR-13): events are pushed to window.dataLayer by autostvabaks.js; no PII --}}
    <script nonce="{{ $nonce }}">window.dataLayer = window.dataLayer || [];</script>
    @if ($gtmId)
        {{-- Google Tag Manager – injected only with a configured container on an analytics-enabled host (App\Support\Analytics) --}}
        <script nonce="{{ $nonce }}">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    @endif

    {{-- Additional tags: admin panel → Настройки → head scripts (scripts receive the CSP nonce) --}}
    {!! Csp::withNonce($settings['head_scripts'] ?? '') !!}
</head>
<body class="{{ $isHome ? 'asv-home' : 'asv-inner' }}">
    @if ($gtmId)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
    @endif
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

    <script src="{{ Site::asset('assets/js/bootstrap.bundle.min.js') }}" nonce="{{ $nonce }}" defer></script>
    @if ($needsSwiper)
        <script src="{{ Site::asset('assets/js/swiper-bundle.min.js') }}" nonce="{{ $nonce }}" defer></script>
    @endif
    @stack('scripts')
    <script src="{{ Site::asset('assets/js/autostvabaks.js') }}" nonce="{{ $nonce }}" defer></script>
</body>
</html>
