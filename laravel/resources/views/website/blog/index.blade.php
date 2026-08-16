@extends('website.layouts.app')

@section('title', __('website.blog') . ' - ' . __('website.site_name'))

@push('styles')
    @vite(['resources/js/website/blog.js'])
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.blog'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="web-blog-tag">{{ __('website.blog_our_blog') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">{{ __('website.blog_page_title') }}</h1>
            <p class="text-sm text-slate-500 mt-3">{{ __('website.blog_page_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8">
                @if ($posts->isEmpty())
                    <div class="web-blog-empty">
                        <i class="fa-solid fa-newspaper"></i>
                        <p class="text-sm font-semibold text-slate-600 mt-3">{{ __('website.blog_empty_title') }}</p>
                        <p class="text-sm text-slate-400 mt-1">{{ __('website.blog_empty_subtitle') }}</p>
                    </div>
                @else
                    @if ($posts->currentPage() === 1 && ($featured = $posts->first()))
                        <a href="{{ route('web.blog.show', ['lang' => current_lang(), 'slug' => $featured->slug]) }}" class="web-blog-featured">
                            <img class="web-blog-featured-img" src="{{ $featured->image_url }}" alt="{{ $featured->title }}">
                            <div class="web-blog-featured-body">
                                <span class="web-blog-tag w-fit">{{ $featured->category }}</span>
                                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-3 leading-snug">{{ $featured->title }}</h2>
                                <p class="text-sm text-slate-500 mt-2 line-clamp-3">{{ $featured->excerpt }}</p>
                                <div class="web-blog-card-meta">
                                    <span>{{ $featured->author_name }}</span>&middot;<span>{{ $featured->published_at?->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endif

                    @if ($categories->isNotEmpty())
                        <div class="web-blog-filter-tabs">
                            <a href="{{ route('web.blog.index', ['lang' => current_lang()]) }}" class="web-blog-filter-tab {{ request('category') ? '' : 'active' }}">{{ __('website.blog_tab_all') }}</a>
                            @foreach ($categories as $category)
                                <a href="{{ route('web.blog.index', ['lang' => current_lang(), 'category' => $category['slug']]) }}" class="web-blog-filter-tab {{ request('category') === $category['slug'] ? 'active' : '' }}">{{ $category['name'] }}</a>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach ($posts as $post)
                            <article class="web-blog-card">
                                <a href="{{ route('web.blog.show', ['lang' => current_lang(), 'slug' => $post->slug]) }}">
                                    <img class="web-blog-card-img" src="{{ $post->image_url }}" alt="{{ $post->title }}">
                                </a>
                                <div class="web-blog-card-body">
                                    <span class="web-blog-tag w-fit">{{ $post->category }}</span>
                                    <a href="{{ route('web.blog.show', ['lang' => current_lang(), 'slug' => $post->slug]) }}">
                                        <h3 class="web-blog-card-title">{{ $post->title }}</h3>
                                    </a>
                                    <p class="web-blog-card-excerpt line-clamp-2">{{ $post->excerpt }}</p>
                                    <div class="web-blog-card-meta">
                                        <span>{{ $post->author_name }}</span>&middot;<span>{{ $post->published_at?->format('M j') }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{ $posts->links() }}
                @endif
            </div>

            <aside class="lg:col-span-4">
                <div class="web-blog-sidebar-card">
                    <h3>{{ __('website.blog_search') }}</h3>
                    <form class="web-blog-search-box" data-blog-search>
                        <input type="text" placeholder="{{ __('website.blog_search_placeholder') }}">
                        <button type="submit" aria-label="{{ __('website.blog_search') }}"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <div class="web-blog-sidebar-card">
                    <h3>{{ __('website.blog_categories') }}</h3>
                    @forelse ($categories as $category)
                        <a href="{{ route('web.blog.index', ['lang' => current_lang(), 'category' => $category['slug']]) }}" class="web-blog-category-item">
                            {{ $category['name'] }} <span class="count">{{ $category['count'] }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400">{{ __('website.blog_no_categories') }}</p>
                    @endforelse
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
