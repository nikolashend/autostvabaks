@php
    use App\Support\Site;

    // Structured data for search engines / AI assistants.
    $business = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'AutoDealer',
        'name' => config('app.name'),
        'url' => Site::route('home'),
        'description' => __('site.meta.description'),
        'image' => asset('assets/imgs/hero/hero.jpg'),
        'email' => Site::setting('public_email'),
        'telephone' => Site::setting('phone'),
        'areaServed' => ['@type' => 'Country', 'name' => 'Estonia'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Meistri 20',
            'addressLocality' => 'Tallinn',
            'postalCode' => '13517',
            'addressCountry' => 'EE',
        ],
    ]);

    $faqSchema = $faqs->isEmpty() ? null : [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq->t('question'),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->t('answer')],
        ])->values()->all(),
    ];

    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;
@endphp
<script type="application/ld+json">{!! json_encode($business, $flags) !!}</script>
@if ($faqSchema)
    <script type="application/ld+json">{!! json_encode($faqSchema, $flags) !!}</script>
@endif
