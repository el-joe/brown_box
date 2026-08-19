@extends('website.layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 web-home">

        <h1 class="sr-only">{{ __('website.site_name') }}</h1>

        {{-- ================= HERO ================= --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 h-64 sm:h-80 lg:h-full">
                <div class="swiper hero-swiper rounded-2xl overflow-hidden h-full bg-slate-900">
                    <div class="swiper-wrapper">
                        @forelse ($banners as $banner)
                            <div class="swiper-slide">
                                <a href="{{ $banner->link() }}" class="block relative h-full">
                                    <picture>
                                        <?php $bannerWebp = webp_url($banner->image) ?>
                                        @if ($bannerWebp)
                                            <source srcset="{{ $bannerWebp }}" type="image/webp">
                                        @endif
                                        <img src="{{ asset_url($banner->image) }}"
                                            alt="{{ $banner->title }}" class="absolute inset-0 w-full h-full object-cover opacity-80"
                                            width="1200" height="400"
                                            {{ $loop->first ? 'fetchpriority="high" loading="eager"' : 'loading="lazy" decoding="async"' }}>
                                    </picture>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent flex flex-col justify-end p-6">
                                        <h2 class="text-white text-2xl font-bold mt-1">{{ $banner->title }}</h2>
                                    </div>
                                </a>
                            </div>
                        @empty
                            @forelse ($flashSale?->items->take(4) ?? [] as $item)
                                @continue(! $item->product)
                                <div class="swiper-slide">
                                    <a href="{{ route('web.products.show', ['lang' => current_lang(), 'slug' => $item->product->slug]) }}" class="block relative h-full">
                                        <img src="{{ $item->product->main_image?->url ?? 'https://placehold.co/900x400/1a1a1a/ffffff?text=Flash+Sale' }}"
                                            alt="{{ $item->product->name }}" class="absolute inset-0 w-full h-full object-cover opacity-80">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent flex flex-col justify-end p-6">
                                            <span class="text-accent text-xs font-semibold uppercase tracking-wide">{{ __('website.flash_sale') }}</span>
                                            <h2 class="text-white text-2xl font-bold mt-1">{{ $item->product->name }}</h2>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <div class="relative h-full">
                                        <img src="https://placehold.co/900x400/1a1a1a/ffffff?text={{ __('website.site_name') }}" class="absolute inset-0 w-full h-full object-cover" alt="{{ __('website.site_name') }}">
                                    </div>
                                </div>
                            @endforelse
                        @endforelse
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>

            <div class="grid grid-rows-2 gap-4">
                @foreach ($newArrivals->take(2) as $sideProduct)
                    <a href="{{ route('web.products.show', ['lang' => current_lang(), 'slug' => $sideProduct->slug]) }}"
                        class="web-promo-banner group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $loop->first ? 'from-indigo-600 to-purple-700' : 'from-slate-700 to-slate-900' }} p-5 flex flex-col justify-between min-h-[140px]">
                        <img src="{{ $sideProduct->main_image?->url ?? 'https://placehold.co/500x300' }}" alt="{{ $sideProduct->name }}"
                            class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:scale-105 transition-transform duration-500">
                        <span class="relative text-xs font-semibold bg-white/20 backdrop-blur px-3 py-1 rounded-full w-fit text-white">{{ $sideProduct->category?->name }}</span>
                        <div class="relative text-white">
                            <p class="text-lg font-extrabold leading-tight line-clamp-1">{{ $sideProduct->name }}</p>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold mt-1">{{ __('website.shop') }} <i class="fa-solid fa-arrow-{{ current_lang() === 'ar' ? 'left' : 'right' }}"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- 30% off pill banner --}}
        @if (setting('hero_banner_enabled', '1') === '1')
            <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}"
                class="mt-4 flex items-center justify-between gap-4 rounded-full bg-gradient-to-r from-brand to-brand text-white px-6 sm:px-10 py-4 overflow-hidden">
                <i class="fa-solid fa-gift text-2xl hidden sm:block"></i>
                <div class="text-center flex-1">
                    <p class="font-bold text-base sm:text-lg">
                        {{ setting('hero_banner_title_' . current_lang()) ?: setting('hero_banner_title_en', __('website.hero_banner_title')) }}
                    </p>
                    <p class="text-xs text-white/80">
                        {{ setting('hero_banner_subtitle_' . current_lang()) ?: setting('hero_banner_subtitle_en', __('website.hero_banner_subtitle')) }}
                    </p>
                </div>
                <i class="fa-solid fa-arrow-{{ current_lang() === 'ar' ? 'left' : 'right' }}"></i>
            </a>
        @endif

        {{-- ================= SHOP BY CATEGORIES ================= --}}
        <section id="categories" class="mt-12">
            <div class="web-section-head">
                <h2 class="text-xl font-bold text-slate-900">{{ __('website.shop_by_categories') }}</h2>
                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-view-all">{{ __('website.view_all') }} <i class="fa-solid fa-chevron-{{ current_lang() === 'ar' ? 'left' : 'right' }} text-[10px]"></i></a>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-4 sm:gap-6">
                @foreach ($categories as $category)
                    <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $category->slug]) }}"
                        class="web-category-card block text-center">
                        <span class="web-category-icon">
                            @if ($category->image)
                                <img src="{{ asset_url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid {{ $category->icon ?: 'fa-tag' }}"></i>
                            @endif
                        </span>
                        <span class="web-category-name text-xs sm:text-sm font-medium">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ================= FLASH SALE ================= --}}
        @if ($flashSale && $flashSale->items->isNotEmpty())
            <section class="mt-12" data-flash-ends="{{ $flashSale->ends_at->toIso8601String() }}">
                <div class="web-section-head">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-red-500"></i> {{ __('website.flash_sale') }}
                    </h2>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                        <span>{{ __('website.flash_sale_ends_in') }}</span>
                        <div id="flash-countdown" class="flex items-center gap-1 font-mono">
                            <div class="flex flex-col items-center">
                                <span data-unit="days" class="web-countdown-box">00</span>
                                <span class="mt-0.5 text-[10px] font-normal text-slate-400">{{ __('website.countdown_days') }}</span>
                            </div>
                            <span class="pb-3.5">:</span>
                            <div class="flex flex-col items-center">
                                <span data-unit="hours" class="web-countdown-box">00</span>
                                <span class="mt-0.5 text-[10px] font-normal text-slate-400">{{ __('website.countdown_hours') }}</span>
                            </div>
                            <span class="pb-3.5">:</span>
                            <div class="flex flex-col items-center">
                                <span data-unit="minutes" class="web-countdown-box">00</span>
                                <span class="mt-0.5 text-[10px] font-normal text-slate-400">{{ __('website.countdown_minutes') }}</span>
                            </div>
                            <span class="pb-3.5">:</span>
                            <div class="flex flex-col items-center">
                                <span data-unit="seconds" class="web-countdown-box">00</span>
                                <span class="mt-0.5 text-[10px] font-normal text-slate-400">{{ __('website.countdown_seconds') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper web-product-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($flashSale->items->take(12) as $item)
                            @continue(! $item->product)
                            <div class="swiper-slide">
                                <x-website.product-card :product="$item->product" class="h-full" />
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </section>
        @endif

        {{-- ================= NEW ARRIVALS ================= --}}
        <section class="mt-12">
            <div class="web-section-head">
                <h2 class="text-xl font-bold text-slate-900">{{ __('website.new_arrivals') }}</h2>
                <a href="{{ route('web.products.index', ['lang' => current_lang(), 'sort' => 'newest']) }}" class="web-view-all">{{ __('website.view_all') }} <i class="fa-solid fa-chevron-{{ current_lang() === 'ar' ? 'left' : 'right' }} text-[10px]"></i></a>
            </div>
            @if ($newArrivals->isNotEmpty())
                <div class="swiper web-product-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($newArrivals as $product)
                            <div class="swiper-slide">
                                <x-website.product-card :product="$product" class="h-full" />
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            @else
                <p class="text-sm text-slate-500">{{ __('website.no_products_found') }}</p>
            @endif
        </section>

        {{-- ================= FEATURED PRODUCTS ================= --}}
        <section class="mt-12">
            <div class="web-section-head">
                <h2 class="text-xl font-bold text-slate-900">{{ __('website.featured_products') }}</h2>
                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-view-all">{{ __('website.view_all') }} <i class="fa-solid fa-chevron-{{ current_lang() === 'ar' ? 'left' : 'right' }} text-[10px]"></i></a>
            </div>
            @if ($featuredProducts->isNotEmpty())
                <div class="swiper web-product-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($featuredProducts as $product)
                            <div class="swiper-slide">
                                <x-website.product-card :product="$product" class="h-full" />
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            @else
                <p class="text-sm text-slate-500">{{ __('website.no_products_found') }}</p>
            @endif
        </section>

        {{-- ================= PER-CATEGORY SECTIONS ================= --}}
        @php
            $bannerGradients = ['from-blue-600 to-sky-500', 'from-brand to-sky-500', 'from-blue-700 to-indigo-600'];
        @endphp
        @foreach ($categorySections as $index => $section)
            <section class="mt-12">
                <h2 class="text-xl font-semibold pl-3 border-s-4 border-brand mb-5">{{ $section['category']->name }}</h2>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $section['category']->slug]) }}"
                        class="web-promo-banner web-category-section-banner relative overflow-hidden lg:col-span-3 bg-gradient-to-br {{ $bannerGradients[$index % count($bannerGradients)] }} min-h-[280px] p-6 flex flex-col justify-between">
                        @if ($section['category']->image)
                            <img src="{{ asset_url($section['category']->image) }}" class="absolute inset-0 w-full h-full object-cover opacity-40" alt="{{ $section['category']->name }}">
                        @endif
                        <span class="relative text-xs font-semibold bg-white/20 backdrop-blur px-3 py-1 rounded-full w-fit text-white">{{ $section['category']->name }}</span>
                        <div class="relative text-white">
                            <p class="text-3xl font-extrabold leading-none">{{ __('website.shop_by_categories') }}</p>
                            <span class="inline-flex items-center gap-2 text-sm font-semibold mt-3">{{ __('website.shop') }} <i class="fa-solid fa-arrow-{{ current_lang() === 'ar' ? 'left' : 'right' }}"></i></span>
                        </div>
                    </a>

                    <div class="lg:col-span-9">
                        <div class="swiper web-product-swiper h-full">
                            <div class="swiper-wrapper">
                                @foreach ($section['products'] as $product)
                                    <div class="swiper-slide">
                                        <x-website.product-card :product="$product" class="h-full" />
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach

        {{-- ================= TOP BRANDS ================= --}}
        @if ($brands->isNotEmpty())
            <section class="mt-12">
                <div class="web-section-head">
                    <h2 class="text-xl font-bold text-slate-900">{{ __('website.top_brands') }}</h2>
                </div>
                <div class="swiper brand-swiper">
                    <div class="swiper-wrapper items-center">
                        @foreach ($brands as $brand)
                            <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'brand_id' => $brand->id]) }}" class="swiper-slide block">
                                <div class="relative h-28 rounded-xl overflow-hidden group shadow-sm">
                                    @if ($brand->logo)
                                        <img src="{{ asset_url($brand->logo) }}" alt="{{ $brand->name }}" loading="lazy"
                                            class="absolute inset-0 w-full h-full object-cover transition-transform group-hover:scale-105">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-white bg-black/50 backdrop-blur-sm rounded-lg px-3 py-1.5 truncate max-w-[90%]">{{ $brand->name }}</span>
                                        </div>
                                    @else
                                        <div class="absolute inset-0 bg-gradient-to-br from-brand/20 to-brand/40"></div>
                                        <div class="relative h-full flex items-center justify-center">
                                            <span class="text-sm font-semibold text-slate-700">{{ $brand->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/home.js'])
@endpush
