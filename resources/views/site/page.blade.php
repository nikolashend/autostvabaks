@extends('site.layout')

@php
    use App\Support\Site;
@endphp

@section('content')
    <div class="asv-page-head">
        <picture class="asv-page-head__bg" aria-hidden="true">
            <source type="image/webp" srcset="{{ asset('assets/imgs/page-header/banner.webp') }}">
            <img src="{{ asset('assets/imgs/page-header/banner.jpg') }}" alt="" decoding="async">
        </picture>
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
                <a class="btn btn-brand-2" href="{{ Site::anchor('paring') }}">{{ __('site.nav.cta') }} <x-asv-icon name="arrow-right" size="16" /></a>
            </aside>
        </div>
    </section>
@endsection
