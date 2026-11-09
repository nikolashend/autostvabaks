@php
    use App\Support\Site;

    $isHome = $isHome ?? false;
    $alternates = $alternates ?? [];
    $metaTitle = $metaTitle ?? config('app.name');
    $metaDescription = $metaDescription ?? __('site.meta.description');
    $canonical = $alternates[$currentLocale] ?? url()->current();
    $ogImage = asset('assets/imgs/hero/hero.jpg');
    // Swiper only serves the reviews carousel: skip it entirely when there is nothing to show.
    $needsSwiper = isset($testimonials) && $testimonials->isNotEmpty();
@endphp
<!doctype html>
<html lang="{{ $currentLocale }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400..800&display=swap" rel="stylesheet">

    {{-- Carento theme (only the parts this site uses) + project styles --}}
    <link href="{{ Site::asset('assets/css/vendors/bootstrap.min.css') }}" rel="stylesheet">
    @if ($needsSwiper)
        <link href="{{ Site::asset('assets/css/plugins/swiper-bundle.min.css') }}" rel="stylesheet">
    @endif
    <link href="{{ Site::asset('assets/css/theme.css') }}" rel="stylesheet">
    <link href="{{ Site::asset('assets/css/autostvabaks.css') }}" rel="stylesheet">

    @stack('head')

    {{-- Analytics / Google Ads tags: admin panel → Настройки --}}
    {!! $settings['head_scripts'] ?? '' !!}
</head>
<body class="{{ $isHome ? 'asv-home' : 'asv-inner' }}">
    <a class="asv-skip" href="#main">{{ __('site.nav.home') }}</a>

    @include('site.partials.header')

    <main class="main" id="main">
        @yield('content')
    </main>

    @include('site.partials.footer')
    @include('site.partials.quick-contact')

    @stack('modals')

    <script src="{{ Site::asset('assets/js/bootstrap.bundle.min.js') }}" defer></script>
    @if ($needsSwiper)
        <script src="{{ Site::asset('assets/js/swiper-bundle.min.js') }}" defer></script>
    @endif
    @stack('scripts')
    <script src="{{ Site::asset('assets/js/autostvabaks.js') }}" defer></script>
</body>
</html>
