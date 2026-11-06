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
        'phone' => '',
        'whatsapp' => '',
        'telegram' => '',
        'public_email' => 'info@autostvabaks.ee',
        'leads_email' => env('LEADS_MAIL_TO', 'alan@autostvabaks.ee'),
        'address' => 'Meistri 20, Tallinn 13517',
        'map_query' => 'Meistri 20, 13517 Tallinn, Estonia',
        'operator_name' => 'Motion Motors OÜ',
        'head_scripts' => '',
    ],
];
