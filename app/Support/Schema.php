<?php

namespace App\Support;

use App\Models\Faq;
use Illuminate\Support\Collection;

/**
 * JSON-LD for the public site. One graph, one factual model:
 *
 *   Organization  = Motion Motors OÜ (legal operator / data controller)
 *   Brand         = Autostvabaks (public brand of the service)
 *   Service       = car buyback, provided by the Organization, area = Estonia
 *   WebSite / WebPage, FAQPage (same source as the visible FAQ)
 *
 * Only client-confirmed facts are emitted: no ratings, no opening hours, no
 * phone until one is configured. See AUTOSTVABAKS_FACT_CLAIM_REGISTER_V1.md.
 */
class Schema
{
    public const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;

    /** @param  Collection<int, Faq>|null  $faqs */
    public static function home(?Collection $faqs = null): array
    {
        $home = Site::route('home', [], Site::defaultLocale());
        $orgId = $home.'#organization';
        $brandId = $home.'#brand';
        $siteId = $home.'#website';
        $serviceId = $home.'#service';
        $pageUrl = Site::route('home');

        $graph = [
            array_filter([
                '@type' => 'Organization',
                '@id' => $orgId,
                'name' => Site::legalEntity(),
                'url' => $home,
                'email' => Site::setting('public_email'),
                'telephone' => Site::setting('phone'),
                'brand' => ['@id' => $brandId],
                'logo' => asset(config('autostvabaks.brand.logo')),
                'address' => static::address(),
                'areaServed' => Site::fact('service_area'),
            ]),
            [
                '@type' => 'Brand',
                '@id' => $brandId,
                'name' => Site::fact('brand'),
                'url' => $home,
                'logo' => asset(config('autostvabaks.brand.logo')),
            ],
            [
                '@type' => 'WebSite',
                '@id' => $siteId,
                'name' => Site::fact('brand'),
                'url' => $home,
                'publisher' => ['@id' => $orgId],
                'inLanguage' => array_keys(Site::locales()),
            ],
            [
                '@type' => 'WebPage',
                '@id' => $pageUrl.'#webpage',
                'url' => $pageUrl,
                'name' => __('site.meta.title'),
                'description' => __('site.meta.description'),
                'inLanguage' => app()->getLocale(),
                'isPartOf' => ['@id' => $siteId],
                'about' => ['@id' => $serviceId],
                'primaryImageOfPage' => Site::ogImage(),
            ],
            [
                '@type' => 'Service',
                '@id' => $serviceId,
                'name' => __('site.hero.eyebrow'),
                'serviceType' => 'Car buyback',
                'description' => __('site.meta.description'),
                'provider' => ['@id' => $orgId],
                'brand' => ['@id' => $brandId],
                'areaServed' => Site::fact('service_area'),
                'availableChannel' => [
                    '@type' => 'ServiceChannel',
                    'serviceUrl' => $pageUrl.'#paring',
                    'availableLanguage' => array_map(
                        fn (array $l) => ['@type' => 'Language', 'name' => $l['name']],
                        array_values(Site::locales()),
                    ),
                ],
            ],
        ];

        if ($faqs && $faqs->isNotEmpty()) {
            $graph[] = [
                '@type' => 'FAQPage',
                '@id' => $pageUrl.'#faq',
                'mainEntity' => $faqs->map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq->t('question'),
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->t('answer')],
                ])->values()->all(),
            ];
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    public static function address(): array
    {
        $a = Site::fact('address', []);

        return [
            '@type' => 'PostalAddress',
            'streetAddress' => $a['street'] ?? '',
            'addressLocality' => $a['city'] ?? '',
            'postalCode' => $a['postal_code'] ?? '',
            'addressCountry' => $a['country'] ?? 'EE',
        ];
    }

    public static function json(array $data): string
    {
        return json_encode($data, static::JSON_FLAGS);
    }
}
