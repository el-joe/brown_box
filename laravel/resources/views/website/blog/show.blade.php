@extends('website.layouts.app')

@section('title', ($post->title ?? __('website.blog')) . ' - ' . __('website.site_name'))

@push('styles')
    @vite(['resources/js/website/blog.js'])
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.blog'), 'url' => route('web.blog.index', ['lang' => current_lang()])],
            ['label' => $post->title ?? $slug, 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <article class="lg:col-span-8">
                @if (!$post)
                    <div class="web-blog-empty">
                        <i class="fa-solid fa-newspaper"></i>
                        <p class="text-sm font-semibold text-slate-600 mt-3">{{ __('website.blog_post_not_found_title') }}</p>
                        <p class="text-sm text-slate-400 mt-1">{{ __('website.blog_post_not_found_subtitle') }}</p>
                        <a href="{{ route('web.blog.index', ['lang' => current_lang()]) }}" class="web-btn-primary mt-4 inline-flex">{{ __('website.blog_back_to_blog') }}</a>
                    </div>
                @else
                    <span class="web-blog-tag">{{ $post->category }}</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 leading-snug">{{ $post->title }}</h1>

                    <div class="web-blog-article-meta mt-4">
                        <img class="web-blog-article-avatar" src="{{ $post->author_avatar_url }}" alt="{{ $post->author_name }}">
                        <div>
                            <p class="font-semibold text-sm text-slate-900">{{ $post->author_name }}</p>
                            <p class="text-xs text-slate-400">{{ $post->published_at?->format('F j, Y') }} &middot; {{ $post->read_time }}</p>
                        </div>
                    </div>

                    <img class="web-blog-article-hero-img mt-6" src="{{ $post->image_url }}" alt="{{ $post->title }}">

                    <div class="web-blog-article-content mt-6">
                        {!! $post->content !!}
                    </div>

                    @if ($relatedPosts->isNotEmpty())
                        <div class="mt-12">
                            <h2 class="text-lg font-extrabold text-slate-900 mb-5">{{ __('website.blog_related_articles') }}</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                @foreach ($relatedPosts as $related)
                                    <article class="web-blog-card">
                                        <a href="{{ route('web.blog.show', ['lang' => current_lang(), 'slug' => $related->slug]) }}">
                                            <img class="web-blog-card-img" src="{{ $related->image_url }}" alt="{{ $related->title }}">
                                        </a>
                                        <div class="web-blog-card-body">
                                            <a href="{{ route('web.blog.show', ['lang' => current_lang(), 'slug' => $related->slug]) }}">
                                                <h3 class="web-blog-card-title text-sm">{{ $related->title }}</h3>
                                            </a>
                                            <div class="web-blog-card-meta"><span>{{ $related->published_at?->format('M j, Y') }}</span></div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </article>

            <aside class="lg:col-span-4">
                <div class="web-blog-sidebar-card">
                    <h3>{{ __('website.blog_search') }}</h3>
                    <form class="web-blog-search-box" data-blog-search>
                        <input type="text" placeholder="{{ __('website.blog_search_placeholder') }}">
                        <button type="submit" aria-label="{{ __('website.blog_search') }}"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <div class="web-blog-sidebar-card" style="padding:0; border:none; background:transparent;">
                    <div class="web-newsletter-mini-card">
                        <i class="fa-solid fa-envelope-open-text text-2xl"></i>
                        <h3 class="text-white mt-3 mb-0">{{ __('website.blog_newsletter_title') }}</h3>
                        <p class="text-xs text-white/80 mt-1">{{ __('website.blog_newsletter_subtitle') }}</p>
                        <form data-newsletter-mini>
                            <input type="email" required placeholder="{{ __('website.newsletter_placeholder') }}">
                            <button type="submit">{{ __('website.newsletter_subscribe') }}</button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
