@extends('site.layout')

@php
    use App\Support\Site;
@endphp

@section('content')
    <div class="asv-page-head">
        <div class="container">
            <nav class="asv-breadcrumb" aria-label="breadcrumb">
                <a href="{{ Site::route('home') }}">{{ __('site.nav.home') }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $page->t('title') }}</span>
            </nav>
            <h1>{{ $page->t('title') }}</h1>
            @if ($page->updated_at)
                <p class="asv-page-head__meta">{{ __('site.page.updated', ['date' => $page->updated_at->format('d.m.Y')]) }}</p>
            @endif
        </div>
    </div>

    <section class="asv-section asv-section--page">
        <div class="container">
            <article class="asv-prose">
                {!! $page->t('content') !!}
            </article>

            <aside class="asv-page-cta">
                <div>
                    <h2>{{ __('site.page.cta_title') }}</h2>
                    <p>{{ __('site.page.cta_text') }}</p>
                </div>
                <a class="asv-btn asv-btn--primary asv-btn--lg" href="{{ Site::anchor('paring') }}">{{ __('site.nav.cta') }} <x-asv-icon name="arrow-right" size="18" class="asv-icon--arrow" /></a>
            </aside>
        </div>
    </section>
@endsection
