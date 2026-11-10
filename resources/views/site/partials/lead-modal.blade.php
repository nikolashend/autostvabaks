@php
    use App\Models\Lead;

    $maxPhotos = (int) config('autostvabaks.leads.max_photos');
    $maxPhotoMb = (int) config('autostvabaks.leads.max_photo_mb');
@endphp

{{-- Step 2 of the request: opened by "Alusta päringut", sent by "Saada päring" --}}
<div class="modal fade asv-modal" id="lead-modal" tabindex="-1" aria-labelledby="lead-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">

            <form id="lead-step2" novalidate>
                <div class="modal-header">
                    <button class="asv-modal__back" type="button" data-bs-dismiss="modal" aria-label="{{ __('site.modal.back') }}">
                        <x-asv-icon name="arrow-left" size="18" /> <span>{{ __('site.modal.back') }}</span>
                    </button>
                    <div class="asv-modal__heading">
                        <h2 class="modal-title" id="lead-modal-title">{{ __('site.modal.title') }}</h2>
                        <span class="asv-modal__step">{{ __('site.form.step', ['n' => 2, 'total' => 2]) }}</span>
                    </div>
                    <button class="asv-modal__close" type="button" data-bs-dismiss="modal" aria-label="{{ __('site.modal.close') }}">
                        <x-asv-icon name="x" size="20" />
                    </button>
                </div>

                <div class="modal-body">
                    <p class="asv-modal__vehicle">
                        <x-asv-icon name="car" size="18" />
                        <span>{{ __('site.modal.vehicle') }}: <strong data-lead-summary></strong></span>
                    </p>

                    <div class="asv-field" data-field="language">
                        <span class="asv-label" id="lead-language-label">{{ __('site.modal.language') }}</span>
                        <div class="asv-segmented" role="radiogroup" aria-labelledby="lead-language-label">
                            @foreach (Lead::LANGUAGES as $code => $label)
                                <label>
                                    <input type="radio" name="language" value="{{ $code }}" @checked($code === $currentLocale)>
                                    <span lang="{{ $code }}">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="asv-error" role="alert"></p>
                    </div>

                    <div class="asv-field" data-field="mileage">
                        <label for="lead-mileage">{{ __('site.modal.mileage') }} <span class="asv-optional">({{ __('site.form.optional') }})</span></label>
                        <div class="asv-affix">
                            <input class="asv-input" id="lead-mileage" name="mileage" type="text" inputmode="numeric" autocomplete="off" maxlength="12"
                                   placeholder="{{ __('site.modal.mileage_placeholder') }}" data-thousands aria-describedby="lead-mileage-error">
                            <span class="asv-affix__unit" aria-hidden="true">km</span>
                        </div>
                        <p class="asv-error" id="lead-mileage-error" role="alert"></p>
                    </div>

                    <div class="asv-field" data-field="info">
                        <label for="lead-info">{{ __('site.modal.info') }} <span class="asv-optional">({{ __('site.form.optional') }})</span></label>
                        <textarea class="asv-input" id="lead-info" name="info" rows="4" maxlength="5000"
                                  placeholder="{{ __('site.modal.info_placeholder') }}" aria-describedby="lead-info-hint lead-info-error"></textarea>
                        <p class="asv-hint" id="lead-info-hint">{{ __('site.modal.info_hint') }}</p>
                        <p class="asv-error" id="lead-info-error" role="alert"></p>
                    </div>

                    <div class="asv-field" data-field="photos">
                        <span class="asv-label" id="lead-photos-label">{{ __('site.modal.photos') }} <span class="asv-optional">({{ __('site.form.optional') }})</span></span>
                        <div class="asv-photos">
                            <ul class="asv-photos__list" data-photo-list aria-labelledby="lead-photos-label"></ul>
                            <label class="asv-photos__add" for="lead-photos">
                                <x-asv-icon name="camera" size="26" />
                                <span>
                                    <strong>{{ __('site.modal.photos_button') }}</strong>
                                    <small>{{ __('site.modal.photos_why') }}</small>
                                </span>
                            </label>
                            <input class="visually-hidden" id="lead-photos" type="file" accept="image/jpeg,image/png,image/webp" multiple
                                   aria-describedby="lead-photos-hint lead-photos-error">
                        </div>
                        <p class="asv-hint" id="lead-photos-hint">{{ __('site.modal.photos_hint', ['max' => $maxPhotos, 'mb' => $maxPhotoMb]) }}</p>
                        <p class="asv-error" id="lead-photos-error" role="alert"></p>
                    </div>

                    {{-- Honeypot: hidden from people, tempting for bots --}}
                    <div class="asv-hp" aria-hidden="true">
                        <label for="lead-website">Website</label>
                        <input id="lead-website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <p class="asv-error asv-error--form" data-form-error role="alert"></p>
                </div>

                <div class="modal-footer">
                    <button class="asv-btn asv-btn--primary asv-btn--lg asv-btn--block" type="submit" data-submit>
                        <span data-submit-label>{{ __('site.modal.submit') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true"></span>
                    </button>
                    <p class="asv-card__note">{{ __('site.form.note') }}</p>
                </div>
            </form>

            {{-- Shown instead of the form after a successful submit --}}
            <div class="asv-success d-none" data-lead-success tabindex="-1">
                <span class="asv-success__icon"><x-asv-icon name="check" size="32" /></span>
                <h2 data-success-title>{{ __('site.modal.success_title') }}</h2>
                <p data-success-text>{{ __('site.modal.success_text') }}</p>
                <div class="asv-success__next">
                    <h3>{{ __('site.modal.success_next_title') }}</h3>
                    <ol>
                        @foreach (__('site.modal.success_next') as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ol>
                </div>
                <div class="asv-success__actions">
                    <button class="asv-btn asv-btn--dark" type="button" data-bs-dismiss="modal">{{ __('site.modal.close') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>
