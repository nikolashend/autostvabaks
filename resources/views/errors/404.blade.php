@extends('site.layout', ['metaTitle' => __('site.not_found.title').' – '.config('app.name'), 'noindex' => true])

@section('content')
    <section class="asv-section asv-notfound">
        <div class="container text-center">
            <p class="asv-notfound__code" aria-hidden="true">404</p>
            <h1>{{ __('site.not_found.title') }}</h1>
            <p class="asv-lead-text mx-auto">{{ __('site.not_found.text') }}</p>
            <a class="btn btn-brand-2 mt-3" href="{{ App\Support\Site::route('home') }}">{{ __('site.not_found.button') }}</a>
        </div>
    </section>
@endsection
