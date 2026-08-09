@extends('website.layouts.app')

@section('title', $page->title . ' - ' . __('website.site_name'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => $page->title, 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="web-policy-hero">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">{{ $page->title }}</h1>
            <span class="web-policy-updated"><i class="fa-regular fa-clock"></i> {{ __('website.policy_last_updated', ['date' => $page->updated_at?->translatedFormat('F j, Y')]) }}</span>
        </div>

        <div class="mt-10">
            {!! $page->content !!}
        </div>
    </section>
@endsection
