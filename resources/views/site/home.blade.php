@extends('site.layout', ['isHome' => true])

@php
    use App\Support\Site;

    $stepIcons = ['send', 'tag', 'search', 'key'];
    $whyIcons = ['bell-off', 'map-pin', 'car', 'handshake'];
    $factIcons = ['map-pin', 'car', 'credit-card', 'banknote'];
    $factorIcons = ['calendar', 'gauge', 'wrench', 'alert', 'sliders', 'book', 'trending'];
    $answerIcons = ['credit-card', 'alert', 'banknote'];
    $phone = Site::setting('phone');
    $email = Site::setting('public_email');
    $address = Site::setting('address');
    $whatsapp = Site::whatsappUrl();
    $telegram = Site::telegramUrl();
    $termsUrl = $termsPage?->url();
    $reviewCount = $testimonials->count();
@endphp

@push('head')
    {{-- the backdrop is only part of the first screen on desktop; phones get the visual below the form --}}
    <link rel="preload" as="image" href="{{ asset('assets/imgs/hero/hero.webp') }}" type="image/webp" media="(min-width: 992px)">
    @include('site.partials.schema')
@endpush

@section('content')

    {{-- ============ HERO: copy + step 1 of the request form + fact strip ============ --}}
    <section class="asv-hero" id="paring" aria-labelledby="hero-title">
        <picture class="asv-hero__bg" aria-hidden="true">
            <source media="(min-width: 992px)" type="image/webp" srcset="{{ asset('assets/imgs/hero/hero.webp') }}">
            <source media="(min-width: 992px)" srcset="{{ asset('assets/imgs/hero/hero.jpg') }}">
            {{-- below 992px the picture is display:none; a 1×1 transparent GIF keeps browsers from fetching the desktop file --}}
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="" width="1920" height="893" fetchpriority="high" decoding="async">
        </picture>

        <div class="container">
            <div class="asv-hero__grid">
                <div class="asv-hero__copy">
                    <p class="asv-eyebrow asv-hero__eyebrow">{{ __('site.hero.eyebrow') }}</p>
                    <h1 id="hero-title">{{ __('site.hero.title') }}</h1>
                    <p class="asv-hero__subtitle">{{ __('site.hero.subtitle') }}</p>
                </div>

                <div class="asv-hero__form">
                    <form class="asv-card asv-lead-form" id="lead-step1" novalidate autocomplete="on" aria-labelledby="lead-step1-title">
                        <div class="asv-card__head">
                            <h2 class="asv-card__title" id="lead-step1-title">{{ __('site.form.title') }}</h2>
                            <span class="asv-step-label">{{ __('site.form.step', ['n' => 1, 'total' => 2]) }}</span>
                        </div>

                        <div class="asv-field" data-field="reg_number">
                            <label for="lead-reg">{{ __('site.form.reg') }}</label>
                            <div class="asv-plate">
                                <span class="asv-plate__eu" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 16 16"><g fill="#fc0">@for ($i = 0; $i < 12; $i++)<circle cx="{{ round(8 + 5.6 * cos(deg2rad($i * 30)), 2) }}" cy="{{ round(8 + 5.6 * sin(deg2rad($i * 30)), 2) }}" r=".9"/>@endfor</g></svg>
                                    EST
                                </span>
                                <input id="lead-reg" name="reg_number" type="text" inputmode="text" autocomplete="off" autocapitalize="characters"
                                       spellcheck="false" maxlength="10" placeholder="{{ __('site.form.reg_placeholder') }}"
                                       aria-describedby="lead-reg-hint lead-reg-error" required>
                            </div>
                            <p class="asv-hint" id="lead-reg-hint">{{ __('site.form.reg_hint') }}</p>
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
                                       placeholder="{{ __('site.form.price_placeholder') }}" data-thousands aria-describedby="lead-price-hint lead-price-error">
                                <span class="asv-affix__unit" aria-hidden="true">€</span>
                            </div>
                            <p class="asv-hint" id="lead-price-hint">{{ __('site.form.price_hint') }}</p>
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

                        <button class="asv-btn asv-btn--primary asv-btn--lg asv-btn--block" type="submit">
                            {{ __('site.form.submit') }} <x-asv-icon name="arrow-right" size="20" class="asv-icon--arrow" />
                        </button>
                        <p class="asv-card__note">{{ __('site.form.note') }}</p>
                    </form>
                </div>
            </div>

            {{-- Fact strip: four confirmed facts (F4–F7), the micro-trust layer of the hero --}}
            <ul class="asv-hero__facts" aria-label="{{ __('site.trust.aria') }}">
                @foreach (__('site.trust.items') as $i => $fact)
                    <li class="asv-fact">
                        <span class="asv-fact__icon"><x-asv-icon :name="$factIcons[$i] ?? 'check'" size="18" /></span>
                        <span>
                            <strong>{{ $fact['title'] }}</strong>
                            <span>{{ $fact['text'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>

            {{-- Supporting visual for phones/tablets (lazy, never above the form) --}}
            <picture class="asv-hero__visual" aria-hidden="true">
                <source type="image/webp" srcset="{{ asset('assets/imgs/hero/hero-mobile.webp') }}">
                <img src="{{ asset('assets/imgs/hero/hero-mobile.jpg') }}" alt="" width="900" height="1200" loading="lazy" decoding="async">
            </picture>
        </div>
    </section>

    {{-- ============ HOW IT WORKS ============ --}}
    <section class="asv-section" id="kuidas-tootab" aria-labelledby="steps-title">
        <div class="container">
            <div class="asv-section__head asv-section__head--center">
                <p class="asv-eyebrow">{{ __('site.steps.eyebrow') }}</p>
                <h2 class="asv-h2" id="steps-title">{{ __('site.steps.title') }}</h2>
            </div>

            <ol class="asv-steps">
                @foreach (__('site.steps.items') as $i => $step)
                    <li class="asv-step">
                        <div class="asv-step__marker">
                            <span class="asv-step__icon"><x-asv-icon :name="$stepIcons[$i] ?? 'check'" size="24" /></span>
                            <span class="asv-step__num" aria-hidden="true">{{ $i + 1 }}</span>
                        </div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>

            <div class="asv-section__cta">
                <a class="asv-btn asv-btn--dark asv-btn--lg" href="#paring" data-focus-form>{{ __('site.nav.cta') }} <x-asv-icon name="arrow-right" size="18" class="asv-icon--arrow" /></a>
            </div>
        </div>
    </section>

    {{-- ============ WHY: objections answered with facts ============ --}}
    <section class="asv-section asv-section--muted" id="miks" aria-labelledby="why-title">
        <div class="container">
            <div class="asv-why">
                <div class="asv-section__head mb-0">
                    <p class="asv-eyebrow">{{ __('site.why.eyebrow') }}</p>
                    <h2 class="asv-h2" id="why-title">{{ __('site.why.title') }}</h2>
                    <p class="asv-lead">{{ __('site.why.lead') }}</p>
                </div>
                <div class="asv-why__grid">
                    @foreach (__('site.why.items') as $i => $item)
                        <div class="asv-benefit">
                            <span class="asv-benefit__icon"><x-asv-icon :name="$whyIcons[$i] ?? 'check'" size="22" /></span>
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ VALUATION TRANSPARENCY ============ --}}
    <section class="asv-section asv-section--dark" id="hindamine" aria-labelledby="valuation-title">
        <div class="container">
            <div class="asv-valuation">
                <div class="asv-valuation__intro">
                    <p class="asv-eyebrow">{{ __('site.valuation.eyebrow') }}</p>
                    <h2 class="asv-h2" id="valuation-title">{{ __('site.valuation.title') }}</h2>
                    <p class="asv-valuation__answer">{{ __('site.valuation.answer') }}</p>
                    <p class="asv-valuation__note"><x-asv-icon name="info" size="18" /> <span>{{ __('site.valuation.note') }}</span></p>
                    <div class="asv-valuation__cta">
                        <a class="asv-btn asv-btn--primary asv-btn--lg" href="#paring" data-focus-form>{{ __('site.valuation.cta') }} <x-asv-icon name="arrow-right" size="18" class="asv-icon--arrow" /></a>
                    </div>
                </div>
                <ul class="asv-factors">
                    @foreach (__('site.valuation.factors') as $i => $factor)
                        <li class="asv-factor">
                            <span class="asv-factor__icon"><x-asv-icon :name="$factorIcons[$i] ?? 'check'" size="20" /></span>
                            <span>
                                <strong>{{ $factor['title'] }}</strong>
                                <span>{{ $factor['text'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ============ DIRECT ANSWERS: leasing / damaged / payment ============ --}}
    <section class="asv-section" id="vastused" aria-labelledby="answers-title">
        <div class="container">
            <div class="asv-section__head asv-section__head--center">
                <p class="asv-eyebrow">{{ __('site.answers.eyebrow') }}</p>
                <h2 class="asv-h2" id="answers-title">{{ __('site.answers.title') }}</h2>
            </div>

            <div class="asv-answers">
                @foreach (__('site.answers.items') as $i => $item)
                    <article class="asv-answer">
                        <span class="asv-answer__icon"><x-asv-icon :name="$answerIcons[$i] ?? 'check'" size="22" /></span>
                        <h3>{{ $item['question'] }}</h3>
                        <p class="asv-answer__answer">{{ $item['answer'] }}</p>
                        <p class="asv-answer__detail">{{ $item['detail'] }}</p>
                        <a class="asv-link" href="#paring" data-focus-form>{{ __('site.answers.cta') }} <x-asv-icon name="arrow-right" size="16" /></a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ REVIEWS (only real, published reviews; see Testimonial::published) ============ --}}
    @if ($reviewCount)
        <section class="asv-section asv-section--muted" id="tagasiside" aria-labelledby="reviews-title">
            <div class="container">
                <div class="asv-section__head asv-section__head--center">
                    <p class="asv-eyebrow">{{ __('site.testimonials.eyebrow') }}</p>
                    <h2 class="asv-h2" id="reviews-title">{{ __('site.testimonials.title') }}</h2>
                </div>

                @php $slider = $reviewCount > 3; @endphp
                <div class="asv-reviews {{ $slider ? 'asv-reviews--slider' : 'asv-reviews--'.$reviewCount }}">
                    @if ($slider)<div class="swiper-container asv-reviews__slider"><div class="swiper-wrapper">@endif
                    @foreach ($testimonials as $testimonial)
                        @if ($slider)<div class="swiper-slide">@endif
                        <figure class="asv-review">
                            @if ($testimonial->rating)
                                <div class="asv-review__stars" role="img" aria-label="{{ __('site.testimonials.rating', ['n' => $testimonial->rating]) }}">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <x-asv-icon name="star" size="18" class="{{ $s <= $testimonial->rating ? 'is-on' : '' }}" />
                                    @endfor
                                </div>
                            @endif
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
                        @if ($slider)</div>@endif
                    @endforeach
                    @if ($slider)</div></div>@endif
                </div>

                @if ($slider)
                    <div class="asv-reviews__controls">
                        <button class="asv-reviews__nav asv-reviews__nav--prev" type="button" aria-label="{{ __('site.testimonials.prev') }}"><x-asv-icon name="arrow-left" size="20" /></button>
                        <div class="asv-reviews__dots"></div>
                        <button class="asv-reviews__nav asv-reviews__nav--next" type="button" aria-label="{{ __('site.testimonials.next') }}"><x-asv-icon name="arrow-right" size="20" /></button>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ============ FAQ (managed in the admin panel) ============ --}}
    @if ($faqs->isNotEmpty())
        <section class="asv-section {{ $reviewCount ? '' : 'asv-section--muted' }}" id="kkk" aria-labelledby="faq-title">
            <div class="container">
                <div class="asv-section__head asv-section__head--center">
                    <p class="asv-eyebrow">{{ __('site.faq.eyebrow') }}</p>
                    <h2 class="asv-h2" id="faq-title">{{ __('site.faq.title') }}</h2>
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

    {{-- ============ LOCATION / CONTACT ============ --}}
    <section class="asv-section" id="kontakt" aria-labelledby="contact-title">
        <div class="container">
            <div class="asv-contact">
                <div class="asv-location">
                    <p class="asv-eyebrow">{{ __('site.contact.eyebrow') }}</p>
                    <h2 class="asv-h2" id="contact-title">{{ __('site.contact.title') }}</h2>
                    {{-- "write or call" only once a public phone number exists (phone = PENDING until then) --}}
                    <p class="asv-lead">{{ __($phone ? 'site.contact.lead_with_phone' : 'site.contact.lead') }}</p>

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

                    <div class="asv-location__footer">
                        <a class="asv-link" href="{{ Site::mapLinkUrl() }}" target="_blank" rel="noopener">{{ __('site.contact.open_map') }} <x-asv-icon name="external" size="16" /></a>
                    </div>
                </div>

                {{-- Click-to-load map: the Google iframe (≈16 requests) is only fetched on request --}}
                <div class="asv-map" data-map-src="{{ Site::mapEmbedUrl() }}" data-map-title="{{ __('site.contact.map_title') }}">
                    <button class="asv-map__load" type="button" data-map-load>
                        <span class="asv-map__pin"><x-asv-icon name="map-pin" size="28" /></span>
                        <strong>{{ $address }}</strong>
                        <span class="asv-btn asv-btn--ghost-light asv-map__btn">{{ __('site.contact.load_map') }}</span>
                        <small>{{ __('site.contact.load_map_note') }}</small>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FINAL CTA ============ --}}
    <section class="asv-section pt-0" aria-labelledby="final-title">
        <div class="container">
            <div class="asv-final">
                <div>
                    <h2 id="final-title">{{ __('site.final.title') }}</h2>
                    <p>{{ __('site.final.text') }}</p>
                </div>
                <a class="asv-btn asv-btn--primary asv-btn--lg" href="#paring" data-focus-form>{{ __('site.final.cta') }} <x-asv-icon name="arrow-right" size="18" class="asv-icon--arrow" /></a>
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
