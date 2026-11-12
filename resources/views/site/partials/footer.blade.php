@php
    use App\Support\Site;

    $anchor = fn (string $id) => $isHome ? '#'.$id : Site::anchor($id);
    $phone = Site::setting('phone');
    $email = Site::setting('public_email');
    $address = Site::setting('address');
@endphp

<footer class="asv-footer">
    <div class="container">
        <div class="asv-footer__grid">
            {{-- 1. Brand --}}
            <div class="asv-footer__brand">
                <a class="asv-logo" href="{{ Site::route('home') }}" aria-label="{{ config('app.name') }}">
                    <x-asv-logo variant="light" />
                </a>
                <p class="asv-footer__about">{{ __('site.footer.about') }}</p>
            </div>

            {{-- 2. Address / phone / e-mail (phone only when configured) --}}
            <div>
                <h3 class="asv-footer__title">{{ __('site.footer.contact') }}</h3>
                <ul>
                    @if ($address)
                        <li>
                            <x-asv-icon name="map-pin" size="16" />
                            <a href="{{ Site::mapLinkUrl() }}" target="_blank" rel="noopener">{{ $address }}</a>
                        </li>
                    @endif
                    @if ($phone)
                        <li>
                            <x-asv-icon name="phone" size="16" />
                            <a href="{{ Site::phoneHref() }}">{{ $phone }}</a>
                        </li>
                    @endif
                    @if ($email)
                        <li>
                            <x-asv-icon name="mail" size="16" />
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- 3. Site sections --}}
            <div>
                <h3 class="asv-footer__title">{{ __('site.footer.links') }}</h3>
                <ul>
                    <li><a href="{{ $anchor('kuidas-tootab') }}">{{ __('site.nav.how') }}</a></li>
                    <li><a href="{{ $anchor('miks') }}">{{ __('site.nav.why') }}</a></li>
                    <li><a href="{{ $anchor('kkk') }}">{{ __('site.nav.faq') }}</a></li>
                    <li><a href="{{ $anchor('kontakt') }}">{{ __('site.nav.contact') }}</a></li>
                    @foreach ($footerInfoPages as $footerPage)
                        <li><a href="{{ $footerPage->url() }}">{{ $footerPage->t('title') }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- 4. Legal --}}
            <div>
                @if ($footerLegalPages->isNotEmpty())
                    <h3 class="asv-footer__title">{{ __('site.footer.legal') }}</h3>
                    <ul>
                        @foreach ($footerLegalPages as $footerPage)
                            <li><a href="{{ $footerPage->url() }}">{{ $footerPage->t('title') }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="asv-footer__bottom">
            <div>
                <p>© {{ date('Y') }} {{ config('app.name') }}. {{ __('site.footer.rights') }}</p>
                {{-- Legal operator / data controller (fact F2): discreet relationship line, no second brand identity --}}
                <p class="asv-footer__operator">{{ __('site.footer.operator', ['operator' => Site::legalEntity(), 'code' => Site::fact('registry_code')]) }}</p>
            </div>
            <a class="asv-footer__top" href="#main">{{ __('site.footer.top') }} <x-asv-icon name="arrow-up" size="16" /></a>
        </div>
    </div>
</footer>
