<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Small helpers shared by the public site: locale-aware URLs and contact links.
 */
class Site
{
    public static function defaultLocale(): string
    {
        return config('autostvabaks.default_locale', 'et');
    }

    /** @return array<string, array{name: string, short: string, og: string}> */
    public static function locales(): array
    {
        return config('autostvabaks.locales', []);
    }

    public static function isLocale(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, static::locales());
    }

    /**
     * URL of a public route in the given (or current) language.
     * The default language has no prefix; the others use the "l." route twin.
     */
    public static function route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale === static::defaultLocale() || ! static::isLocale($locale)) {
            return route($name, $parameters);
        }

        return route('l.'.$name, ['locale' => $locale] + $parameters);
    }

    /** Home page URL with an anchor, e.g. Site::anchor('kkk'). */
    public static function anchor(string $id, ?string $locale = null): string
    {
        return static::route('home', [], $locale).'#'.$id;
    }

    public static function setting(string $key, ?string $default = null): ?string
    {
        $value = Setting::get($key, $default);

        return ($value === null || trim($value) === '') ? $default : trim($value);
    }

    public static function phoneHref(?string $phone = null): ?string
    {
        $phone ??= static::setting('phone');
        $digits = preg_replace('/[^\d+]/', '', (string) $phone);

        return $digits ? 'tel:'.$digits : null;
    }

    public static function whatsappUrl(): ?string
    {
        $number = preg_replace('/\D/', '', (string) static::setting('whatsapp'));

        return $number ? 'https://wa.me/'.$number : null;
    }

    public static function telegramUrl(): ?string
    {
        $value = (string) static::setting('telegram');

        if ($value === '') {
            return null;
        }

        if (preg_match('~^https?://~i', $value)) {
            return $value;
        }

        // "+372 5xxx xxxx" → t.me/+3725xxxxxxx, "@name" / "name" → t.me/name
        if (preg_match('/^\+?[\d\s()-]+$/', $value)) {
            return 'https://t.me/+'.preg_replace('/\D/', '', $value);
        }

        return 'https://t.me/'.ltrim($value, '@');
    }

    public static function mapEmbedUrl(): string
    {
        return 'https://www.google.com/maps?q='.rawurlencode((string) static::setting('map_query', static::setting('address'))).'&z=15&output=embed';
    }

    public static function mapLinkUrl(): string
    {
        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode((string) static::setting('map_query', static::setting('address')));
    }

    /** Cache-busted URL of a file under /public. */
    public static function asset(string $path): string
    {
        $file = public_path($path);

        return asset($path).(is_file($file) ? '?v='.filemtime($file) : '');
    }
}
