@props([
    // "dark" = graphite wordmark for light backgrounds (logo-av-04), "light" = white wordmark for dark backgrounds (logo-av-05)
    'variant' => 'dark',
    'height' => 30,
])
@php
    // Approved Autostvabaks wordmark (resources/brand/source/svg → optimised copies in public/assets/imgs/brand).
    // The horizontal variant is the primary logo; the intrinsic ratio of the cropped SVG is 351.97 × 47.61.
    $file = $variant === 'light' ? 'logo-white.svg' : 'logo.svg';
    $width = (int) round($height * 351.97 / 47.61);
@endphp
<img {{ $attributes->merge(['class' => 'asv-logo__img']) }} src="{{ \App\Support\Site::asset('assets/imgs/brand/'.$file) }}" alt="{{ config('app.name') }}" width="{{ $width }}" height="{{ $height }}" decoding="async">
