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

    /**
     * The single hreflang generator: one absolute URL per locale for the same
     * page. Used for <link rel="alternate">, canonical and the sitemap, so the
     * language versions are always reciprocal.
     *
     * @param  \Closure(string $locale): string  $urlFor
     * @return array<string, string>
     */
    public static function alternates(\Closure $urlFor): array
    {
        $alternates = [];
        foreach (array_keys(static::locales()) as $locale) {
            $alternates[$locale] = $urlFor($locale);
        }

        return $alternates;
    }

    /** @return array<string, string> */
    public static function routeAlternates(string $name, array $parameters = []): array
    {
        return static::alternates(fn (string $locale) => static::route($name, $parameters, $locale));
    }

    /** Client-confirmed business fact from config/autostvabaks.php ("facts.*"). */
    public static function fact(string $key, mixed $default = null): mixed
    {
        return config('autostvabaks.facts.'.$key, $default);
    }

    public static function legalEntity(): string
    {
        return static::setting('operator_name', (string) static::fact('legal_entity'));
    }

    /** True once a contact channel (phone, whatsapp, telegram) has a public value. */
    public static function hasChannel(string $channel): bool
    {
        return match ($channel) {
            'phone' => static::phoneHref() !== null,
            'whatsapp' => static::whatsappUrl() !== null,
            'telegram' => static::telegramUrl() !== null,
            default => static::setting($channel) !== null,
        };
    }

    public static function isProductionHost(): bool
    {
        return parse_url((string) config('app.url'), PHP_URL_HOST) === config('autostvabaks.production_host');
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

    /**
     * Cache-busted URL of a file under /public. For .css/.js sources the minified
     * twin (name.min.ext, built by scripts/build-assets.cjs) is served when it
     * exists and is not older than the source.
     */
    public static function asset(string $path): string
    {
        $file = public_path($path);

        if ($min = static::minifiedTwin($path)) {
            $path = $min;
            $file = public_path($min);
        }

        return asset($path).(is_file($file) ? '?v='.filemtime($file) : '');
    }

    /** Path of the up-to-date minified twin of a .css/.js source, or null. */
    public static function minifiedTwin(string $path): ?string
    {
        if (! config('autostvabaks.minified_assets', true) || str_contains($path, '.min.') || ! preg_match('/\.(css|js)$/', $path)) {
            return null;
        }

        $min = preg_replace('/\.(css|js)$/', '.min.$1', $path);
        $source = public_path($path);
        $target = public_path($min);

        return is_file($target) && is_file($source) && filemtime($target) >= filemtime($source) ? $min : null;
    }

    /** Production-safe social image for the current locale (brand composition, never the temporary hero photo). */
    public static function ogImage(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $path = 'assets/imgs/brand/og-'.$locale.'.jpg';

        if (! is_file(public_path($path))) {
            $path = 'assets/imgs/brand/og-'.static::defaultLocale().'.jpg';
        }

        return asset($path);
    }
}
