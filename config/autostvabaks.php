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
    | The prototype palette (lime #70f46d on #101010) is NOT the approved brand.
    | Approved brand direction: magenta #E6007E, graphite #141414, white — to be
    | applied in Stage 2 from the approved SVG logo files. CSS reads the same
    | tokens from :root in public/assets/css/autostvabaks.css; the admin panel
    | and <meta name="theme-color"> read them from here.
    */
    'brand' => [
        'primary' => env('BRAND_PRIMARY', '#70f46d'),
        'ink' => env('BRAND_INK', '#101010'),
        'approved' => ['primary' => '#E6007E', 'ink' => '#141414', 'surface' => '#FFFFFF'],
        'assets_status' => 'PENDING', // approved SVG logo / favicon not yet in the repository
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

        // Photos are attached to the e-mail until this total is reached;
        // the rest stay available in the admin panel only.
        'mail_attachments_max_mb' => 18,

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
];
