<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site languages
    |--------------------------------------------------------------------------
    | The default locale is served without a URL prefix (/), the others with
    | one (/ru, /en). Google Ads campaigns link straight to a language version;
    | there is deliberately no automatic redirect by browser language.
    */
    'default_locale' => 'et',

    // true на тестовом домене: robots.txt закрывает сайт, на всех страницах <meta name="robots" content="noindex">
    'noindex' => (bool) env('SITE_NOINDEX', false),

    // "user:password" — закрывает весь сайт HTTP Basic Auth (только тестовый домен)
    'staging_auth' => env('STAGING_AUTH', ''),

    /*
    |--------------------------------------------------------------------------
    | Production canonical host
    |--------------------------------------------------------------------------
    | The one host search engines should index. `site:release-check` refuses a
    | build whose APP_URL points here while SITE_NOINDEX is still on.
    */
    'production_host' => 'autostvabaks.ee',

    /*
    |--------------------------------------------------------------------------
    | Central business facts (see docs/autostvabaks/AUTOSTVABAKS_FACT_CLAIM_REGISTER_V1.md)
    |--------------------------------------------------------------------------
    | Static, client-confirmed facts live here; contact channels that may change
    | (phone, messengers, e-mails) live in the admin Settings and fall back to
    | the `settings` defaults below. Nothing here may be an unverified claim.
    */
    'facts' => [
        'brand' => 'Autostvabaks',
        'legal_entity' => 'Motion Motors OÜ',
        // Stage 4 (client-supplied, authoritative): company registry code and VAT number of the operator
        'registry_code' => '17341567',
        'vat_number' => 'EE102908916',
        'address' => [
            'street' => 'Meistri 20',
            'city' => 'Tallinn',
            'postal_code' => '13517',
            'country' => 'EE',
        ],
        'service_area' => ['@type' => 'Country', 'name' => 'Estonia'],
        'payment_methods' => ['bank_transfer', 'cash'],
        // Public status of contact channels: a channel is CONFIRMED once its
        // value is filled in the admin panel; until then it must not appear.
        'pending_channels' => ['phone', 'whatsapp', 'telegram'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Brand / design tokens
    |--------------------------------------------------------------------------
    | Approved identity (Stage 2): the colour values are read from the approved
    | SVG logo files in resources/brand/source/svg (magenta #E6007E, graphite
    | #141414, white). CSS carries the same values as :root tokens in
    | public/assets/css/autostvabaks.css; the admin panel and <meta
    | name="theme-color"> read them from here. Web logo files:
    | public/assets/imgs/brand/{logo.svg, logo-white.svg, favicon.svg}.
    */
    'brand' => [
        'primary' => env('BRAND_PRIMARY', '#E6007E'),
        'ink' => env('BRAND_INK', '#141414'),
        'approved' => ['primary' => '#E6007E', 'ink' => '#141414', 'surface' => '#FFFFFF'],
        'assets_status' => 'APPLIED', // resources/brand/source (originals) + public/assets/imgs/brand (web)
        'logo' => 'assets/imgs/brand/logo.svg',
        'logo_light' => 'assets/imgs/brand/logo-white.svg',
    ],

    'locales' => [
        'et' => ['name' => 'Eesti', 'short' => 'ET', 'og' => 'et_EE'],
        'ru' => ['name' => 'Русский', 'short' => 'RU', 'og' => 'ru_RU'],
        'en' => ['name' => 'English', 'short' => 'EN', 'og' => 'en_GB'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead form
    |--------------------------------------------------------------------------
    */
    'leads' => [
        // Fallback recipient; the primary value is the "leads_email" setting in the admin panel.
        'mail_to' => env('LEADS_MAIL_TO', 'alan@autostvabaks.ee'),

        'max_photos' => 10,
        'max_photo_mb' => 8,

        // Photos are attached to the e-mail until this total is reached; the rest
        // stay available in the admin panel only (the e-mail links to the lead).
        // 10 MB of files ≈ 14 MB on the wire – safely under common 20–25 MB limits.
        'mail_attachments_max_mb' => (int) env('LEADS_MAIL_ATTACHMENTS_MAX_MB', 10),

        // Browser-side downscale before upload (keeps uploads fast on mobile
        // and the notification e-mail small enough to be delivered).
        'client_resize_px' => 1920,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default settings (overridable in the admin panel → Настройки)
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'phone' => '',      // PENDING – client has not chosen the public number yet
        'whatsapp' => '',   // PENDING
        'telegram' => '',   // PENDING
        'public_email' => 'info@autostvabaks.ee',
        'leads_email' => env('LEADS_MAIL_TO', 'alan@autostvabaks.ee'),
        'address' => 'Meistri 20, Tallinn 13517',
        'map_query' => 'Meistri 20, 13517 Tallinn, Estonia',
        'operator_name' => 'Motion Motors OÜ',
        'head_scripts' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Placeholder / draft markers
    |--------------------------------------------------------------------------
    | Strings that identify seeded sample content. Matching testimonials are
    | never rendered in production, and `site:release-check` fails while any of
    | them is still visible on a public page.
    */
    'placeholder_markers' => [
        'Näidistagasiside', 'Siia tuleb', 'Kliendi nimi', 'Lorem ipsum', 'Demo review',
        'Example review', 'Sample review', 'Пример отзыва', 'Здесь будет отзыв',
        'A real customer review will appear here',
    ],
    'draft_markers' => ['Mustand', 'Черновик', 'Draft'],

    /*
    |--------------------------------------------------------------------------
    | Prohibited claims (FACT_CLAIM_REGISTER §4)
    |--------------------------------------------------------------------------
    | Lower-case fragments that must never appear in rendered public pages.
    | Checked by tests/Feature/BrandFrontendTest.php and `site:release-check`.
    */
    'prohibited_claims' => [
        'parim hind', 'parima hinna', 'kõrgeim hind', 'kõrgeima hinna', 'garanteeri', 'kiireim', 'tasuta hindamine', 'nr 1', 'nr. 1',
        'лучшая цена', 'лучшую цену', 'самая высокая цена', 'гарантир', 'самый быстрый', 'лучший выкуп', 'обязательно купим',
        'best price', 'highest price', 'guarantee', 'fastest', '#1', 'number one', 'free valuation',
    ],

    /*
    |--------------------------------------------------------------------------
    | Production release gates (Stage 3) – checked by `site:release-check`
    |--------------------------------------------------------------------------
    | Human sign-offs are recorded as explicit statuses, never inferred from
    | "the file exists" or "the route works". On the production host anything
    | other than "approved" blocks the release.
    |
    |   HERO_ASSET_STATUS    temporary | approved   – the hero photo/render licence + brand fit
    |   LEGAL_CONTENT_STATUS draft     | approved   – Terms & Privacy texts approved by the client
    */
    'release' => [
        'hero_asset_status' => env('HERO_ASSET_STATUS', 'temporary'),
        'legal_content_status' => env('LEGAL_CONTENT_STATUS', 'draft'),
        // Files that identified the temporary (template) hero visual. They were deleted in Stage 4
        // (ADR-32); the gate still refuses any reference to them in og:image / schema / hero and
        // blocks "approved" while one of them exists on disk.
        'temporary_hero_files' => [
            'assets/imgs/hero/hero.jpg', 'assets/imgs/hero/hero.webp',
            'assets/imgs/hero/hero-mobile.jpg', 'assets/imgs/hero/hero-mobile.webp',
            'assets/imgs/hero/hero-mobile-card.jpg', 'assets/imgs/hero/hero-mobile-card.webp',
        ],
        // Production hero (Stage 4): Pexels photo 20695262 by Erik Mclean, Pexels licence (free for
        // commercial use, modification allowed, no attribution required); crops in public/assets/imgs/hero/hero-coast.*
        'hero_files' => [
            'assets/imgs/hero/hero-coast.avif', 'assets/imgs/hero/hero-coast.webp', 'assets/imgs/hero/hero-coast.jpg',
            'assets/imgs/hero/hero-coast-mobile.webp', 'assets/imgs/hero/hero-coast-mobile.jpg',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics vendor (Google Tag Manager)
    |--------------------------------------------------------------------------
    | The public pages push events to window.dataLayer (see ADR-13). The GTM
    | container is injected only when an ID is configured AND analytics are
    | enabled for this environment (default: production host only). Vendor tags
    | live in the container, never in templates. Additional snippets may still
    | be pasted in admin → Settings → head scripts.
    */
    'analytics' => [
        'gtm_id' => env('ANALYTICS_GTM_ID', ''),
        // null = enabled automatically on the production host only; true/false force it (e.g. GTM preview on staging)
        'enabled' => env('ANALYTICS_ENABLED'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content-Security-Policy for the public site (ADR-24)
    |--------------------------------------------------------------------------
    | off | report-only | enforce. Applies to public routes only – Filament /
    | Livewire under /admin need inline/eval and keep the Stage-1 headers.
    */
    'csp' => env('CSP_MODE', 'report-only'),

    /*
    |--------------------------------------------------------------------------
    | Production assets
    |--------------------------------------------------------------------------
    | `node scripts/build-assets.cjs` writes autostvabaks.min.css / .min.js next
    | to the readable sources. Site::asset() serves the .min twin when it exists
    | and is not older than the source (so an edited source never ships stale).
    */
    'minified_assets' => (bool) env('ASSETS_MINIFIED', true),
];
