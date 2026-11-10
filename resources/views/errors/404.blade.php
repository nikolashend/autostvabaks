@extends('site.layout', ['metaTitle' => __('site.not_found.title').' – '.config('app.name'), 'noindex' => true])

@section('content')
    <section class="asv-section asv-notfound">
        <div class="container">
            <p class="asv-notfound__code" aria-hidden="true">404</p>
            <h1>{{ __('site.not_found.title') }}</h1>
            <p class="asv-lead">{{ __('site.not_found.text') }}</p>
            <a class="asv-btn asv-btn--dark asv-btn--lg" href="{{ App\Support\Site::route('home') }}">{{ __('site.not_found.button') }}</a>
        </div>
    </section>
@endsection
