{{--
    Mobile sticky CTA (≤ 991px). Shown by JS once the hero form has scrolled out of view,
    hidden again while the step-2 modal / mobile menu is open and when the footer is visible.
--}}
<div class="asv-sticky" data-sticky-cta aria-hidden="true">
    <p class="asv-sticky__note">{{ __('site.sticky.note') }}</p>
    <a class="asv-btn asv-btn--primary" href="#paring" data-focus-form data-cta-location="sticky" tabindex="-1">
        {{ __('site.sticky.cta') }} <x-asv-icon name="arrow-right" size="18" class="asv-icon--arrow" />
    </a>
</div>
