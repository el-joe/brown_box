@extends('website.layouts.app')

@section('title', ($post->title ?? __('website.blog')) . ' - ' . __('website.site_name'))

@push('scripts')
    @vite(['resources/js/website/blog.js'])

    @if ($post)
        @php
            $articleSchema = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'description' => $post->excerpt ?: Illuminate\Support\Str::limit(strip_tags($post->content), 160),
                'image' => $post->image_url ?? null,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
                'author' => ['@type' => 'Person', 'name' => $post->author_name ?? setting('site_name_en', config('app.name'))],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => setting('site_name_en', config('app.name')),
                    'logo' => ['@type' => 'ImageObject', 'url' => asset_url(setting('site_logo'))],
                ],
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url()->current()],
            ]);
        @endphp
        <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
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
            <article class="lg:col-span-8" @if($post) itemscope itemtype="https://schema.org/BlogPosting" @endif>
                @if (!$post)
                    <div class="web-blog-empty">
                        <i class="fa-solid fa-newspaper"></i>
                        <p class="text-sm font-semibold text-slate-600 mt-3">{{ __('website.blog_post_not_found_title') }}</p>
                        <p class="text-sm text-slate-400 mt-1">{{ __('website.blog_post_not_found_subtitle') }}</p>
                        <a href="{{ route('web.blog.index', ['lang' => current_lang()]) }}" class="web-btn-primary mt-4 inline-flex">{{ __('website.blog_back_to_blog') }}</a>
                    </div>
                @else
                    <span class="web-blog-tag">{{ $post->category }}</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 leading-snug" itemprop="headline">{{ $post->title }}</h1>

                    <div class="web-blog-article-meta mt-4">
                        <img class="web-blog-article-avatar" src="{{ $post->author_avatar_url }}" alt="{{ $post->author_name }}">
                        <div>
                            <p class="font-semibold text-sm text-slate-900">{{ $post->author_name }}</p>
                            <p class="text-xs text-slate-400">{{ $post->published_at?->format('F j, Y') }} &middot; {{ $post->read_time }}</p>
                        </div>
                        <div class="flex items-center gap-2 ml-auto">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" aria-label="{{ __('website.share_on_facebook') }}" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-white hover:bg-blue-600 hover:border-blue-600 transition-colors"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" aria-label="{{ __('website.share_on_x') }}" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-white hover:bg-black hover:border-black transition-colors"><i class="fa-brands fa-x-twitter"></i></a>
                            <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&media={{ urlencode($post->image_url) }}&description={{ urlencode($post->title) }}" target="_blank" rel="noopener" aria-label="{{ __('website.share_on_pinterest') }}" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-white hover:bg-red-600 hover:border-red-600 transition-colors"><i class="fa-brands fa-pinterest-p"></i></a>
                            <button type="button" data-copy-link="{{ url()->current() }}" aria-label="{{ __('website.copy_link') }}" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-white hover:bg-brand hover:border-brand transition-colors"><i class="fa-solid fa-link"></i></button>
                        </div>
                    </div>

                    <img class="web-blog-article-hero-img mt-6" src="{{ $post->image_url }}" alt="{{ $post->title }}" itemprop="image">

                    <div class="web-blog-article-content mt-6" itemprop="articleBody">
                        {!! $post->content !!}
                    </div>
                    <meta itemprop="datePublished" content="{{ $post->published_at?->toIso8601String() }}">
                    <meta itemprop="dateModified" content="{{ $post->updated_at?->toIso8601String() }}">
                    <span itemprop="author" itemscope itemtype="https://schema.org/Person" class="hidden">
                        <meta itemprop="name" content="{{ $post->author_name }}">
                    </span>

                    @if ($post->category)
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <span class="text-sm font-bold text-slate-900 mr-1">{{ __('website.blog_categories') }}:</span>
                            <span class="web-blog-tag">{{ $post->category }}</span>
                        </div>
                    @endif

                    <!-- Author bio -->
                    <div class="mt-8 flex items-start gap-4 bg-slate-50 border border-slate-100 rounded-2xl p-5">
                        <img class="w-16 h-16 rounded-full object-cover shrink-0" src="{{ $post->author_avatar_url }}" alt="{{ $post->author_name }}">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900">{{ $post->author_name }}</p>
                            <p class="text-sm text-slate-500 mt-2">{{ $post->excerpt }}</p>
                        </div>
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

                @if ($post && $relatedPosts->isNotEmpty())
                    <div class="web-blog-sidebar-card">
                        <h3>{{ __('website.blog_related_articles') }}</h3>
                        @foreach ($relatedPosts as $recent)
                            <a href="{{ route('web.blog.show', ['lang' => current_lang(), 'slug' => $recent->slug]) }}" class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                                <img class="w-16 h-16 rounded-lg object-cover shrink-0" src="{{ $recent->image_url }}" alt="{{ $recent->title }}">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $recent->title }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $recent->published_at?->format('M j, Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

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
