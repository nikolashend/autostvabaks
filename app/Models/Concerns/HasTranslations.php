<?php

namespace App\Models\Concerns;

/**
 * Minimal translation helper for JSON columns shaped as {et: "...", ru: "...", en: "..."}.
 * The model must cast those columns to "array".
 */
trait HasTranslations
{
    public function t(string $attribute, ?string $locale = null): string
    {
        $values = $this->getAttribute($attribute);

        if (! is_array($values)) {
            return (string) $values;
        }

        $locale ??= app()->getLocale();
        $fallback = config('autostvabaks.default_locale', 'et');

        foreach ([$locale, $fallback] as $candidate) {
            $value = trim((string) ($values[$candidate] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }
}
