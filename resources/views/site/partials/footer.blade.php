@php
    use App\Support\Site;

    $anchor = fn (string $id) => $isHome ? '#'.$id : Site::anchor($id);
    $phone = Site::setting('phone');
    $email = Site::setting('public_email');
    $address = Site::setting('address');
@endphp

<footer class="footer asv-footer">
    <div class="container">
        <div class="row gy-5">
            {{-- 1. Logo / name --}}
            <div class="col-lg-4 col-md-6">
                <a class="asv-logo" href="{{ Site::route('home') }}" aria-label="{{ config('app.name') }}">
                    @include('site.partials.logo')
                </a>
                <p class="asv-footer__about">{{ __('site.footer.about') }}</p>
            </div>

            {{-- 2. Address / phone / e-mail --}}
            <div class="col-lg-3 col-md-6">
                <h3 class="asv-footer__title">{{ __('site.footer.contact') }}</h3>
                <ul class="asv-footer__contacts">
                    @if ($address)
                        <li>
                            <x-asv-icon name="map-pin" size="18" />
                            <a href="{{ Site::mapLinkUrl() }}" target="_blank" rel="noopener">{{ $address }}</a>
                        </li>
                    @endif
                    @if ($phone)
                        <li>
                            <x-asv-icon name="phone" size="18" />
                            <a href="{{ Site::phoneHref() }}">{{ $phone }}</a>
                        </li>
                    @endif
                    @if ($email)
                        <li>
                            <x-asv-icon name="mail" size="18" />
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- 3. Site sections (+ information / SEO pages once they are added in the admin panel) --}}
            <div class="col-lg-2 col-md-6 col-6">
                <h3 class="asv-footer__title">{{ __('site.footer.links') }}</h3>
                <ul class="menu-footer">
                    <li><a href="{{ $anchor('kuidas-tootab') }}">{{ __('site.nav.how') }}</a></li>
                    <li><a href="{{ $anchor('miks') }}">{{ __('site.nav.why') }}</a></li>
                    <li><a href="{{ $anchor('kkk') }}">{{ __('site.nav.faq') }}</a></li>
                    <li><a href="{{ $anchor('kontakt') }}">{{ __('site.nav.contact') }}</a></li>
                </ul>
            </div>

            {{-- 4. Legal --}}
            <div class="col-lg-3 col-md-6 col-6">
                @if ($footerLegalPages->isNotEmpty())
                    <h3 class="asv-footer__title">{{ __('site.footer.legal') }}</h3>
                    <ul class="menu-footer">
                        @foreach ($footerLegalPages as $footerPage)
                            <li><a href="{{ $footerPage->url() }}">{{ $footerPage->t('title') }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($footerInfoPages->isNotEmpty())
                <div class="col-12">
                    <h3 class="asv-footer__title">{{ __('site.footer.info') }}</h3>
                    <ul class="menu-footer asv-footer__inline">
                        @foreach ($footerInfoPages as $footerPage)
                            <li><a href="{{ $footerPage->url() }}">{{ $footerPage->t('title') }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="footer-bottom asv-footer__bottom">
            <p class="text-sm color-white mb-0">© {{ date('Y') }} {{ config('app.name') }}. {{ __('site.footer.rights') }}</p>
            <a class="asv-footer__top" href="#main">{{ __('site.footer.top') }} ↑</a>
        </div>
    </div>
</footer>
