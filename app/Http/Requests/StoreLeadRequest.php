<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Normalise user input before validation: "123 abc" → "123ABC", "15 000 €" → 15000. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reg_number' => mb_strtoupper(preg_replace('/[\s\-]+/u', '', (string) $this->input('reg_number'))),
            'phone' => trim((string) $this->input('phone')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'desired_price' => $this->digitsOrNull($this->input('desired_price')),
            'mileage' => $this->digitsOrNull($this->input('mileage')),
            'info' => trim((string) $this->input('info')) ?: null,
        ]);
    }

    private function digitsOrNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Thousands separators and units are fine ("15 000", "185 000 km", "15000€");
        // anything else is left as is so the numeric rule rejects it.
        $clean = preg_replace('/[\s\x{00A0}\x{202F}.,\'’]|€|eur|km/iu', '', $value);

        return $clean === '' ? $value : $clean;
    }

    public function rules(): array
    {
        $cfg = config('autostvabaks.leads');

        return [
            'reg_number' => ['required', 'string', 'regex:/^[A-Z0-9ÕÄÖÜŠŽ]{2,10}$/u'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^\+?[\d\s()\-]{5,}$/', function ($attribute, $value, $fail) {
                $digits = strlen(preg_replace('/\D/', '', (string) $value));
                if ($digits < 5 || $digits > 15) {
                    $fail(__('site.errors.phone_invalid'));
                }
            }],
            'email' => ['required', 'string', 'max:255', 'email:rfc'],
            'desired_price' => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'terms' => ['accepted'],

            'language' => ['required', Rule::in(array_keys(Lead::LANGUAGES))],
            'mileage' => ['nullable', 'integer', 'min:0', 'max:5000000'],
            'info' => ['nullable', 'string', 'max:5000'],
            'photos' => ['nullable', 'array', 'max:'.$cfg['max_photos']],
            'photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:'.($cfg['max_photo_mb'] * 1024)],
        ];
    }

    public function messages(): array
    {
        $cfg = config('autostvabaks.leads');

        return [
            'reg_number.required' => __('site.errors.reg_required'),
            'reg_number.regex' => __('site.errors.reg_invalid'),
            'phone.required' => __('site.errors.phone_required'),
            'phone.regex' => __('site.errors.phone_invalid'),
            'phone.max' => __('site.errors.phone_invalid'),
            'email.required' => __('site.errors.email_required'),
            'email.email' => __('site.errors.email_invalid'),
            'email.max' => __('site.errors.email_invalid'),
            'desired_price.*' => __('site.errors.price_invalid'),
            'terms.accepted' => __('site.errors.terms'),
            'language.*' => __('site.errors.language'),
            'mileage.*' => __('site.errors.mileage_invalid'),
            'info.max' => __('site.errors.info_max', ['max' => 5000]),
            'photos.max' => __('site.errors.photos_max', ['max' => $cfg['max_photos']]),
            'photos.array' => __('site.errors.photo_type'),
            'photos.*.file' => __('site.errors.photo_type'),
            'photos.*.mimes' => __('site.errors.photo_type'),
            'photos.*.max' => __('site.errors.photo_size', ['max' => $cfg['max_photo_mb']]),
            'photos.*.uploaded' => __('site.errors.photo_size', ['max' => $cfg['max_photo_mb']]),
        ];
    }
}
