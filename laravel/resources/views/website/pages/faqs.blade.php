@extends('website.layouts.app')

@section('title', __('website.faqs') . ' - ' . __('website.site_name'))

@push('styles')
    @vite(['resources/js/website/faq.js'])
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.faqs'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="web-faq-hero">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">{{ __('website.faqs') }}</h1>
            <p class="text-sm text-slate-500 mt-3">{{ __('website.faq_subtitle') }}</p>

            <form id="faq-search-form" class="web-faq-search-box">
                <input id="faq-search-input" type="text" placeholder="{{ __('website.faq_search_placeholder') }}">
                <button type="submit" aria-label="{{ __('website.faq_search_placeholder') }}"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="web-faq-tabs mt-8">
            <button type="button" class="web-faq-tab active" data-category="all">{{ __('website.faq_tab_all') }}</button>
            @foreach ($groups as $group)
                <button type="button" class="web-faq-tab" data-category="{{ $group['key'] }}">{{ $group['title'] }}</button>
            @endforeach
        </div>

        <div class="max-w-3xl mx-auto mt-8">
            @foreach ($groups as $group)
                <div class="web-faq-group" data-category="{{ $group['key'] }}">
                    <h2 class="web-faq-group-title"><i class="fa-solid {{ $group['icon'] }}"></i> {{ $group['title'] }}</h2>

                    @foreach ($group['items'] as $item)
                        <div class="web-faq-item">
                            <button type="button" class="web-faq-question">
                                {{ $item['q'] }}
                                <i class="fa-solid fa-chevron-down web-faq-icon"></i>
                            </button>
                            <div class="web-faq-answer hidden">{!! $item['a'] !!}</div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div id="faq-empty" class="web-faq-empty hidden">
                <i class="fa-solid fa-magnifying-glass"></i>
                <p class="text-sm">{{ __('website.faq_no_results') }}</p>
            </div>
        </div>

        <div class="web-faq-cta max-w-3xl mx-auto mt-14">
            <i class="fa-solid fa-headset text-3xl"></i>
            <h2 class="text-xl sm:text-2xl font-extrabold mt-3">{{ __('website.faq_cta_title') }}</h2>
            <p class="text-sm text-white/80 mt-2">{{ __('website.faq_cta_subtitle') }}</p>
            <a href="{{ route('web.contact.index', ['lang' => current_lang()]) }}" class="web-btn-primary"><i class="fa-solid fa-paper-plane"></i> {{ __('website.faq_cta_button') }}</a>
        </div>
    </section>
@endsection
