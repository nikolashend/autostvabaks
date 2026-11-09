@extends('site.layout', ['isHome' => true])

@php
    use App\Support\Site;

    $stepIcons = ['file', 'tag', 'search', 'cash'];
    $whyIcons = ['car', 'map-pin', 'clock', 'shield'];
    $phone = Site::setting('phone');
    $email = Site::setting('public_email');
    $address = Site::setting('address');
    $whatsapp = Site::whatsappUrl();
    $telegram = Site::telegramUrl();
    $termsUrl = $termsPage?->url();
@endphp

@push('head')
    <link rel="preload" as="image" href="{{ asset('assets/imgs/hero/hero.webp') }}" type="image/webp" media="(min-width: 768px)">
    <link rel="preload" as="image" href="{{ asset('assets/imgs/hero/hero-mobile.webp') }}" type="image/webp" media="(max-width: 767px)">
    @include('site.partials.schema')
@endpush

@section('content')

    {{-- ============ HERO + step 1 of the request form ============ --}}
    <section class="asv-hero" id="paring">
        <picture class="asv-hero__bg" aria-hidden="true">
            <source media="(max-width: 767px)" type="image/webp" srcset="{{ asset('assets/imgs/hero/hero-mobile.webp') }}">
            <source type="image/webp" srcset="{{ asset('assets/imgs/hero/hero.webp') }}">
            <img src="{{ asset('assets/imgs/hero/hero.jpg') }}" alt="" width="1920" height="893" fetchpriority="high" decoding="async">
        </picture>

        <div class="container asv-hero__inner">
            <div class="row gx-lg-5 align-items-start">
                <div class="col-lg-6 order-lg-2">
                    <div class="asv-hero__text">
                        <p class="asv-eyebrow asv-eyebrow--light">{{ __('site.hero.eyebrow') }}</p>
                        <h1>{{ __('site.hero.title') }}</h1>
                        <p class="asv-hero__subtitle">{{ __('site.hero.subtitle') }}</p>
                        <ul class="asv-ticks d-none d-lg-flex">
                            @foreach (__('site.hero.ticks') as $tick)
                                <li><x-asv-icon name="check" size="16" /> {{ $tick }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6 col-xl-5 order-lg-1">
                    <form class="asv-card asv-lead" id="lead-step1" novalidate autocomplete="on">
                        <h2 class="asv-card__title">{{ __('site.form.title') }}</h2>

                        <div class="asv-field" data-field="reg_number">
                            <label for="lead-reg">{{ __('site.form.reg') }}</label>
                            <div class="asv-plate">
                                <span class="asv-plate__eu" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 16 16"><g fill="#fc0">@for ($i = 0; $i < 12; $i++)<circle cx="{{ round(8 + 5.6 * cos(deg2rad($i * 30)), 2) }}" cy="{{ round(8 + 5.6 * sin(deg2rad($i * 30)), 2) }}" r=".9"/>@endfor</g></svg>
                                    EST
                                </span>
                                <input id="lead-reg" name="reg_number" type="text" inputmode="text" autocomplete="off" autocapitalize="characters"
                                       spellcheck="false" maxlength="10" placeholder="{{ __('site.form.reg_placeholder') }}"
                                       aria-describedby="lead-reg-error" required>
                            </div>
                            <p class="asv-error" id="lead-reg-error" role="alert"></p>
                        </div>

                        <div class="row gx-3">
                            <div class="col-sm-6">
                                <div class="asv-field" data-field="phone">
                                    <label for="lead-phone">{{ __('site.form.phone') }}</label>
                                    <input class="asv-input" id="lead-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="32"
                                           placeholder="{{ __('site.form.phone_placeholder') }}" aria-describedby="lead-phone-error" required>
                                    <p class="asv-error" id="lead-phone-error" role="alert"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="asv-field" data-field="email">
                                    <label for="lead-email">{{ __('site.form.email') }}</label>
                                    <input class="asv-input" id="lead-email" name="email" type="email" inputmode="email" autocomplete="email" maxlength="255"
                                           placeholder="{{ __('site.form.email_placeholder') }}" aria-describedby="lead-email-error" required>
                                    <p class="asv-error" id="lead-email-error" role="alert"></p>
                                </div>
                            </div>
                        </div>

                        <div class="asv-field" data-field="desired_price">
                            <label for="lead-price">{{ __('site.form.price') }} <span class="asv-optional">({{ __('site.form.optional') }})</span></label>
                            <div class="asv-affix">
                                <input class="asv-input" id="lead-price" name="desired_price" type="text" inputmode="numeric" autocomplete="off" maxlength="12"
                                       placeholder="{{ __('site.form.price_placeholder') }}" data-thousands aria-describedby="lead-price-error">
                                <span class="asv-affix__unit" aria-hidden="true">€</span>
                            </div>
                            <p class="asv-error" id="lead-price-error" role="alert"></p>
                        </div>

                        <div class="asv-field asv-field--check" data-field="terms">
                            <label class="asv-check" for="lead-terms">
                                <input id="lead-terms" name="terms" type="checkbox" value="1" aria-describedby="lead-terms-error" required>
                                <span>
                                    @if ($termsUrl)
                                        {!! __('site.form.terms', ['link' => '<a href="'.e($termsUrl).'" target="_blank" rel="noopener">'.e(__('site.form.terms_link')).'</a>']) !!}
                                    @else
                                        {{ __('site.form.terms', ['link' => __('site.form.terms_link')]) }}
                                    @endif
                                </span>
                            </label>
                            <p class="asv-error" id="lead-terms-error" role="alert"></p>
                        </div>

                        <button class="btn btn-brand-2 asv-btn-lg w-100" type="submit">
                            {{ __('site.form.submit') }} <x-asv-icon name="arrow-right" size="18" />
                        </button>
                        <p class="asv-card__note">{{ __('site.form.note') }}</p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 4 STEPS ============ --}}
    <section class="asv-section" id="kuidas-tootab">
        <div class="container">
            <div class="asv-section__head text-center">
                <p class="asv-eyebrow">{{ __('site.steps.eyebrow') }}</p>
                <h2>{{ __('site.steps.title') }}</h2>
            </div>

            <ol class="asv-steps">
                @foreach (__('site.steps.items') as $i => $step)
                    <li class="asv-step">
                        <div class="asv-step__top">
                            <span class="asv-step__icon"><x-asv-icon :name="$stepIcons[$i] ?? 'check'" size="26" /></span>
                            <span class="asv-step__num" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                        </div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>

            <div class="asv-section__cta">
                <a class="btn btn-primary" href="#paring" data-focus-form>{{ __('site.nav.cta') }} <x-asv-icon name="arrow-right" size="16" /></a>
            </div>
        </div>
    </section>

    {{-- ============ WHY US ============ --}}
    <section class="asv-section asv-section--tint" id="miks">
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-5">
                    <div class="asv-section__head mb-0">
                        <p class="asv-eyebrow">{{ __('site.why.eyebrow') }}</p>
                        <h2>{{ __('site.why.title') }}</h2>
                        <p class="asv-lead-text">{{ __('site.why.lead') }}</p>
                        <a class="btn btn-brand-2 mt-2" href="#paring" data-focus-form>{{ __('site.nav.cta') }} <x-asv-icon name="arrow-right" size="16" /></a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3 g-md-4">
                        @foreach (__('site.why.items') as $i => $item)
                            <div class="col-sm-6">
                                <div class="asv-benefit">
                                    <span class="asv-benefit__icon"><x-asv-icon :name="$whyIcons[$i] ?? 'check'" size="24" /></span>
                                    <h3>{{ $item['title'] }}</h3>
                                    <p>{{ $item['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ TESTIMONIALS (managed in the admin panel) ============ --}}
    @if ($testimonials->isNotEmpty())
        <section class="asv-section" id="tagasiside">
            <div class="container">
                <div class="asv-section__head text-center">
                    <p class="asv-eyebrow">{{ __('site.testimonials.eyebrow') }}</p>
                    <h2>{{ __('site.testimonials.title') }}</h2>
                </div>

                <div class="asv-reviews">
                    <button class="asv-reviews__nav asv-reviews__nav--prev" type="button" aria-label="{{ __('site.testimonials.prev') }}">
                        <x-asv-icon name="arrow-left" size="20" />
                    </button>

                    <div class="swiper-container asv-reviews__slider">
                        <div class="swiper-wrapper">
                            @foreach ($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <figure class="asv-review">
                                        <div class="asv-review__stars" role="img" aria-label="{{ __('site.testimonials.rating', ['n' => $testimonial->rating]) }}">
                                            @for ($s = 1; $s <= 5; $s++)
                                                <x-asv-icon name="star" size="18" class="{{ $s <= $testimonial->rating ? 'is-on' : '' }}" />
                                            @endfor
                                        </div>
                                        @if ($testimonial->t('title'))
                                            <p class="asv-review__title">{{ $testimonial->t('title') }}</p>
                                        @endif
                                        <blockquote>{{ $testimonial->t('text') }}</blockquote>
                                        <figcaption>
                                            <span class="asv-review__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($testimonial->author, 0, 1)) }}</span>
                                            <span>
                                                <strong>{{ $testimonial->author }}</strong>
                                                @if ($testimonial->t('location'))
                                                    <small>{{ $testimonial->t('location') }}</small>
                                                @endif
                                            </span>
                                        </figcaption>
                                    </figure>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button class="asv-reviews__nav asv-reviews__nav--next" type="button" aria-label="{{ __('site.testimonials.next') }}">
                        <x-asv-icon name="arrow-right" size="20" />
                    </button>
                </div>
                <div class="asv-reviews__dots"></div>
            </div>
        </section>
    @endif

    {{-- ============ FAQ (managed in the admin panel) ============ --}}
    @if ($faqs->isNotEmpty())
        <section class="asv-section asv-section--tint" id="kkk">
            <div class="container">
                <div class="asv-section__head text-center">
                    <p class="asv-eyebrow">{{ __('site.faq.eyebrow') }}</p>
                    <h2>{{ __('site.faq.title') }}</h2>
                </div>

                <div class="accordion asv-faq" id="faq-list">
                    @foreach ($faqs as $i => $faq)
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faq-h-{{ $faq->id }}">
                                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-c-{{ $faq->id }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="faq-c-{{ $faq->id }}">
                                    <span class="asv-faq__num" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                                    <span>{{ $faq->t('question') }}</span>
                                </button>
                            </h3>
                            <div class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" id="faq-c-{{ $faq->id }}" aria-labelledby="faq-h-{{ $faq->id }}" data-bs-parent="#faq-list">
                                <div class="accordion-body">{!! nl2br(e($faq->t('answer'))) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ CONTACT + MAP ============ --}}
    <section class="asv-section" id="kontakt">
        <div class="container">
            <div class="row gy-4 gx-lg-5">
                <div class="col-lg-5">
                    <div class="asv-section__head mb-4">
                        <p class="asv-eyebrow">{{ __('site.contact.eyebrow') }}</p>
                        <h2>{{ __('site.contact.title') }}</h2>
                        {{-- "write or call" only once a public phone number exists (phone = PENDING until then) --}}
                        <p class="asv-lead-text">{{ __($phone ? 'site.contact.lead_with_phone' : 'site.contact.lead') }}</p>
                    </div>

                    <ul class="asv-contacts">
                        @if ($address)
                            <li>
                                <span class="asv-contacts__icon"><x-asv-icon name="map-pin" size="20" /></span>
                                <span>
                                    <small>{{ __('site.contact.address') }}</small>
                                    <a href="{{ Site::mapLinkUrl() }}" target="_blank" rel="noopener">{{ $address }}</a>
                                </span>
                            </li>
                        @endif
                        @if ($phone)
                            <li>
                                <span class="asv-contacts__icon"><x-asv-icon name="phone" size="20" /></span>
                                <span>
                                    <small>{{ __('site.contact.phone') }}</small>
                                    <a href="{{ Site::phoneHref() }}">{{ $phone }}</a>
                                </span>
                            </li>
                        @endif
                        @if ($email)
                            <li>
                                <span class="asv-contacts__icon"><x-asv-icon name="mail" size="20" /></span>
                                <span>
                                    <small>{{ __('site.contact.email') }}</small>
                                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                                </span>
                            </li>
                        @endif
                    </ul>

                    @if ($whatsapp || $telegram)
                        <div class="asv-messengers">
                            @if ($whatsapp)
                                <a class="asv-messenger asv-messenger--whatsapp" href="{{ $whatsapp }}" target="_blank" rel="noopener"><x-asv-icon name="whatsapp" size="20" /> WhatsApp</a>
                            @endif
                            @if ($telegram)
                                <a class="asv-messenger asv-messenger--telegram" href="{{ $telegram }}" target="_blank" rel="noopener"><x-asv-icon name="telegram" size="20" /> Telegram</a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-lg-7">
                    {{-- Click-to-load map: the Google iframe (≈16 requests) is only fetched on request --}}
                    <div class="asv-map" data-map-src="{{ Site::mapEmbedUrl() }}" data-map-title="{{ __('site.contact.map_title') }}">
                        <button class="asv-map__load" type="button" data-map-load>
                            <span class="asv-map__pin"><x-asv-icon name="map-pin" size="28" /></span>
                            <strong>{{ $address }}</strong>
                            <span class="btn btn-brand-2">{{ __('site.contact.load_map') }}</span>
                            <small>{{ __('site.contact.load_map_note') }}</small>
                        </button>
                    </div>
                    <a class="asv-map__link" href="{{ Site::mapLinkUrl() }}" target="_blank" rel="noopener">{{ __('site.contact.open_map') }} <x-asv-icon name="arrow-right" size="14" /></a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('modals')
    @include('site.partials.lead-modal')
@endpush

@push('scripts')
    @php
        $leadFormConfig = [
            'endpoint' => Site::route('leads.store'),
            'locale' => $currentLocale,
            'maxPhotos' => (int) config('autostvabaks.leads.max_photos'),
            'maxPhotoMb' => (int) config('autostvabaks.leads.max_photo_mb'),
            'resizePx' => (int) config('autostvabaks.leads.client_resize_px'),
            'messages' => array_merge(__('site.errors'), [
                'photos_max' => __('site.errors.photos_max', ['max' => config('autostvabaks.leads.max_photos')]),
                'photo_size' => __('site.errors.photo_size', ['max' => config('autostvabaks.leads.max_photo_mb')]),
                'photo_remove' => __('site.modal.photo_remove'),
                'photo_alt' => __('site.modal.photo_alt'),
                'submit' => __('site.modal.submit'),
                'sending' => __('site.modal.sending'),
            ]),
        ];
    @endphp
    <script>window.ASV = {{ Illuminate\Support\Js::from($leadFormConfig) }};</script>
@endpush
