{{-- Structured data: built in App\Support\Schema (single factual model, FAQ shared with the visible accordion) --}}
<script type="application/ld+json">{!! App\Support\Schema::json(App\Support\Schema::home($faqs)) !!}</script>
