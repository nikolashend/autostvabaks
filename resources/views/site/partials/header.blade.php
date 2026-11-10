@php
    use App\Support\Site;

    // On the landing page these are auto-scroll anchors; on inner pages they lead back to it.
    $nav = [
        'kuidas-tootab' => __('site.nav.how'),
        'miks' => __('site.nav.why'),
        'kkk' => __('site.nav.faq'),
        'kontakt' => __('site.nav.contact'),
    ];
    $anchor = fn (string $id) => $isHome ? '#'.$id : Site::anchor($id);
    $phone = Site::setting('phone');
    // landing: transparent over the graphite hero (white wordmark); inner pages: white bar (graphite wordmark)
    $logoVariant = $isHome ? 'light' : 'dark';
@endphp

<header class="asv-header {{ $isHome ? 'asv-header--overlay' : 'asv-header--solid' }}">
    <div class="asv-header__inner">
        <a class="asv-logo" href="{{ Site::route('home') }}" aria-label="{{ config('app.name') }}">
            <x-asv-logo :variant="$logoVariant" />
        </a>

        <nav class="asv-header__nav" aria-label="{{ __('site.nav.menu') }}">
            <ul>
                @foreach ($nav as $id => $label)
                    <li><a href="{{ $anchor($id) }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        </nav>

        <div class="asv-header__right">
            @if ($phone)
                <a class="asv-header__phone" href="{{ Site::phoneHref() }}">
                    <x-asv-icon name="phone" size="18" /> {{ $phone }}
                </a>
            @endif

            <div class="dropdown asv-lang">
                <button class="asv-pill" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('site.nav.language') }}: {{ $locales[$currentLocale]['short'] }}">
                    <x-asv-icon name="globe" size="16" />
                    <span>{{ $locales[$currentLocale]['short'] }}</span>
                    <x-asv-icon name="chevron-down" size="14" />
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach ($locales as $code => $locale)
                        <li>
                            <a class="dropdown-item {{ $code === $currentLocale ? 'active' : '' }}"
                               href="{{ $alternates[$code] ?? Site::route('home', [], $code) }}"
                               hreflang="{{ $code }}" lang="{{ $code }}"
                               @if ($code === $currentLocale) aria-current="true" @endif>
                                <span>{{ $locale['name'] }}</span>
                                @if ($code === $currentLocale)<x-asv-icon name="check" size="16" />@endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a class="asv-btn asv-btn--primary asv-header__cta" href="{{ $anchor('paring') }}" data-focus-form>
                {{ __('site.nav.cta') }}
            </a>

            <button class="asv-pill asv-pill--icon asv-burger" type="button" data-menu-open aria-controls="asv-mobile-menu" aria-expanded="false" aria-label="{{ __('site.nav.menu') }}">
                <x-asv-icon name="menu" size="22" />
            </button>
        </div>
    </div>
</header>

{{-- Mobile menu --}}
<div class="asv-mobile" id="asv-mobile-menu" aria-hidden="true">
    <div class="asv-mobile__backdrop" data-menu-close></div>
    <div class="asv-mobile__panel" role="dialog" aria-modal="true" aria-label="{{ __('site.nav.menu') }}">
        <div class="asv-mobile__head">
            <a class="asv-logo" href="{{ Site::route('home') }}" aria-label="{{ config('app.name') }}"><x-asv-logo variant="dark" loading="lazy" /></a>
            <button class="asv-mobile__close" type="button" data-menu-close aria-label="{{ __('site.modal.close') }}">
                <x-asv-icon name="x" size="22" />
            </button>
        </div>

        <nav aria-label="{{ __('site.nav.menu') }}">
            <ul class="asv-mobile__nav">
                @foreach ($nav as $id => $label)
                    <li><a href="{{ $anchor($id) }}" data-menu-close>{{ $label }} <x-asv-icon name="chevron-right" size="18" /></a></li>
                @endforeach
            </ul>
        </nav>

        <a class="asv-btn asv-btn--primary asv-btn--lg asv-btn--block asv-mobile__cta" href="{{ $anchor('paring') }}" data-menu-close data-focus-form>{{ __('site.nav.cta') }}</a>

        <div class="asv-mobile__langs" aria-label="{{ __('site.nav.language') }}">
            @foreach ($locales as $code => $locale)
                <a class="{{ $code === $currentLocale ? 'is-active' : '' }}"
                   href="{{ $alternates[$code] ?? Site::route('home', [], $code) }}" hreflang="{{ $code }}" lang="{{ $code }}"
                   @if ($code === $currentLocale) aria-current="true" @endif>{{ $locale['short'] }}</a>
            @endforeach
        </div>

        <div class="asv-mobile__contacts">
            @if ($phone)
                <a href="{{ Site::phoneHref() }}"><x-asv-icon name="phone" size="18" /> {{ $phone }}</a>
            @endif
            @if ($email = Site::setting('public_email'))
                <a href="mailto:{{ $email }}"><x-asv-icon name="mail" size="18" /> {{ $email }}</a>
            @endif
        </div>
    </div>
</div>
