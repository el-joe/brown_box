@extends('website.layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 web-home">

        {{-- ================= HERO ================= --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 relative rounded-2xl overflow-hidden h-64 sm:h-80 lg:h-full bg-slate-900">
                @if ($flashSale && $flashSale->items->isNotEmpty())
                    @php($heroProduct = $flashSale->items->first()->product)
                    <a href="{{ $heroProduct ? route('web.products.show', ['lang' => current_lang(), 'slug' => $heroProduct->slug]) : '#' }}" class="block relative h-full">
                        <img src="{{ $heroProduct?->main_image?->url ?? 'https://placehold.co/900x400/1a1a1a/ffffff?text=Flash+Sale' }}"
                            alt="{{ $heroProduct?->name }}" class="absolute inset-0 w-full h-full object-cover opacity-80">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent flex flex-col justify-end p-6">
                            <span class="text-amber-400 text-xs font-semibold uppercase tracking-wide">{{ __('website.flash_sale') }}</span>
                            <h2 class="text-white text-2xl font-bold mt-1">{{ $flashSale->name }}</h2>
                        </div>
                    </a>
                @else
                    <img src="https://placehold.co/900x400/1a1a1a/ffffff?text={{ __('website.site_name') }}" class="absolute inset-0 w-full h-full object-cover" alt="{{ __('website.site_name') }}">
                @endif
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
        <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}"
            class="mt-4 flex items-center justify-between gap-4 rounded-full bg-gradient-to-r from-amber-600 to-amber-500 text-white px-6 sm:px-10 py-4 overflow-hidden">
            <i class="fa-solid fa-gift text-2xl hidden sm:block"></i>
            <div class="text-center flex-1">
                <p class="font-bold text-base sm:text-lg">{{ __('website.hero_banner_title') }}</p>
                <p class="text-xs text-white/80">{{ __('website.hero_banner_subtitle') }}</p>
            </div>
            <i class="fa-solid fa-arrow-{{ current_lang() === 'ar' ? 'left' : 'right' }}"></i>
        </a>

        {{-- ================= SHOP BY CATEGORIES ================= --}}
        <section id="categories" class="mt-12">
            <div class="web-section-head">
                <h2 class="text-xl font-bold text-slate-900">{{ __('website.shop_by_categories') }}</h2>
                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-view-all">{{ __('website.view_all') }} <i class="fa-solid fa-chevron-{{ current_lang() === 'ar' ? 'left' : 'right' }} text-[10px]"></i></a>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-4 sm:gap-6">
                @foreach ($categories as $category)
                    <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $category->slug]) }}"
                        class="web-category-card flex flex-col items-center gap-3 text-center">
                        <span class="web-category-icon">
                            @if ($category->image)
                                <img src="{{ asset_url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <i class="fa-solid {{ $category->icon ?: 'fa-tag' }}"></i>
                            @endif
                        </span>
                        <span class="text-xs sm:text-sm font-medium text-slate-700">{{ $category->name }}</span>
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
                            <span data-unit="days" class="web-countdown-box">00</span>:
                            <span data-unit="hours" class="web-countdown-box">00</span>:
                            <span data-unit="minutes" class="web-countdown-box">00</span>:
                            <span data-unit="seconds" class="web-countdown-box">00</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6">
                    @foreach ($flashSale->items->take(12) as $item)
                        @continue(! $item->product)
                        <x-website.product-card :product="$item->product" />
                    @endforeach
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
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6">
                    @foreach ($newArrivals as $product)
                        <x-website.product-card :product="$product" />
                    @endforeach
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
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6">
                    @foreach ($featuredProducts as $product)
                        <x-website.product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-500">{{ __('website.no_products_found') }}</p>
            @endif
        </section>

        {{-- ================= TOP BRANDS ================= --}}
        @if ($brands->isNotEmpty())
            <section class="mt-12">
                <div class="web-section-head">
                    <h2 class="text-xl font-bold text-slate-900">{{ __('website.top_brands') }}</h2>
                </div>
                <div class="flex flex-wrap items-center gap-6 sm:gap-10">
                    @foreach ($brands as $brand)
                        <div class="grayscale hover:grayscale-0 transition-all opacity-70 hover:opacity-100">
                            @if ($brand->logo)
                                <img src="{{ asset_url($brand->logo) }}" alt="{{ $brand->name }}" class="h-10 object-contain">
                            @else
                                <span class="text-sm font-semibold text-slate-500">{{ $brand->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/home.js'])
@endpush
