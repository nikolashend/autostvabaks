{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($entries as $entry)
@foreach ($entry['alternates'] as $locale => $url)
    <url>
        <loc>{{ $url }}</loc>
@if ($entry['lastmod'])
        <lastmod>{{ $entry['lastmod'] }}</lastmod>
@endif
@foreach ($entry['alternates'] as $altLocale => $altUrl)
        <xhtml:link rel="alternate" hreflang="{{ $altLocale }}" href="{{ $altUrl }}"/>
@endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $entry['alternates'][$defaultLocale] }}"/>
    </url>
@endforeach
@endforeach
</urlset>
