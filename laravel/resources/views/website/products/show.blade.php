@extends('website.layouts.app')

@section('title', $product->name)

@section('content')
    @php
        $mainImage = $product->main_image?->url ?? 'https://placehold.co/600x600';
        $gallery = $product->productImages->isNotEmpty() ? $product->productImages : collect();
        $hasDiscount = $product->is_on_sale;
        $discountPercent = $hasDiscount && $product->price > 0
            ? (int) round((($product->price - $product->effective_price) / $product->price) * 100)
            : 0;
        $stock = $product->has_variants ? $product->variants->sum(fn ($v) => $v->stocks->sum('qty')) : $product->total_stock;
    @endphp

    <div class="max-w-7xl mx-auto px-4 py-8 web-product"
        data-product-id="{{ $product->id }}"
        data-base-price="{{ $product->price }}"
        data-effective-price="{{ $product->effective_price }}"
        data-stock="{{ $stock }}"
        data-has-variants="{{ $product->has_variants ? '1' : '0' }}"
        data-variants='@json($variantsJson)'
        data-attribute-options='@json($attributeOptions)'
        @if ($activeFlashSaleItem) data-flash-ends="{{ $activeFlashSaleItem->flashSale->ends_at->toIso8601String() }}" @endif
    >
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => $product->category?->name, 'url' => $product->category ? route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $product->category->slug]) : null],
            ['label' => $product->name, 'url' => null],
        ]" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mt-5">

            {{-- ============ GALLERY ============ --}}
            <div class="product-gallery-wrap">

                {{-- Main slider --}}
                <div class="swiper product-gallery-main relative rounded-xl overflow-hidden border border-slate-100 bg-slate-50">
                    @if ($hasDiscount)
                        <span class="absolute top-3 start-3 z-10 web-deal-badge">-{{ $discountPercent }}%</span>
                    @endif

                    <div class="swiper-wrapper">
                        @forelse ($gallery as $media)
                            <div class="swiper-slide">
                                @if ($media->type === 'video')
                                    <video
                                        src="{{ $media->url }}"
                                        class="w-full aspect-square object-cover"
                                        controls
                                        preload="metadata"
                                        playsinline>
                                    </video>
                                @else
                                    <img
                                        src="{{ $media->url }}"
                                        alt="{{ $product->name }}"
                                        class="w-full aspect-square object-cover transition-transform duration-500 ease-out hover:scale-125 cursor-zoom-in"
                                        loading="lazy">
                                @endif
                            </div>
                        @empty
                            {{-- Fallback when no images exist --}}
                            <div class="swiper-slide">
                                <img src="https://placehold.co/600x600" alt="{{ $product->name }}" class="w-full aspect-square object-cover transition-transform duration-500 ease-out hover:scale-125 cursor-zoom-in">
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Thumbnail strip — only render if there is more than one media item --}}
                @if ($gallery->count() > 1)
                    <div class="swiper product-gallery-thumbs mt-3">
                        <div class="swiper-wrapper">
                            @foreach ($gallery as $media)
                                <div class="swiper-slide !w-16 !h-16 rounded-lg overflow-hidden border-2 border-transparent cursor-pointer shrink-0">
                                    @if ($media->type === 'video')
                                        {{-- Video thumb: show a play icon overlay on a dark bg --}}
                                        <div class="relative w-full h-full bg-slate-800 flex items-center justify-center">
                                            <i class="fa-solid fa-circle-play text-white text-2xl opacity-80"></i>
                                        </div>
                                    @else
                                        <img src="{{ $media->url }}" alt="{{ $product->name }} thumbnail" class="w-full h-full object-cover">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============ DETAILS ============ --}}
            <div>
                @if ($product->brand)
                    <p class="text-xs font-semibold text-brand uppercase tracking-wide">{{ $product->brand->name }}</p>
                @endif
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $product->name }}</h1>

                <div class="flex items-center gap-3 mt-2.5">
                    <x-website.rating :value="$averageRating" :count="$reviewCount" />
                    <span class="text-slate-300">|</span>
                    @if ($stock > 0)
                        <span class="text-xs text-emerald-600 font-medium"><i class="fa-solid fa-circle-check me-1"></i>{{ __('website.in_stock') }}</span>
                    @else
                        <span class="text-xs text-red-500 font-medium"><i class="fa-solid fa-circle-xmark me-1"></i>{{ __('website.out_of_stock') }}</span>
                    @endif
                </div>

                <div class="flex items-end gap-3 mt-4">
                    <span id="product-price-current" class="text-3xl font-extrabold {{ $hasDiscount ? 'text-red-600' : 'text-brand' }}">
                        {{ money_format($product->effective_price) }}
                    </span>
                    @if ($hasDiscount)
                        <span id="product-price-original" class="text-base text-slate-400 line-through mb-1">{{ money_format($product->price) }}</span>
                        <span id="product-discount-pill" class="web-discount-pill mb-1">{{ __('website.save_percent', ['percent' => $discountPercent]) }}</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">{{ __('website.free_shipping_note', ['amount' => money_format(50)]) }}</p>

                @if ($product->short_description)
                    <p class="text-sm text-slate-600 leading-relaxed mt-4">{{ $product->short_description }}</p>
                @endif

                @if ($activeFlashSaleItem)
                    <div class="mt-5 flex items-center gap-2 rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">
                        <i class="fa-solid fa-bolt"></i>
                        <span>{{ __('website.flash_sale_ends_in') }}:</span>
                        <div id="flash-countdown" class="flex items-center gap-1 font-mono font-semibold">
                            <span data-unit="days">00</span>d
                            <span data-unit="hours">00</span>h
                            <span data-unit="minutes">00</span>m
                            <span data-unit="seconds">00</span>s
                        </div>
                    </div>
                @endif

                {{-- Variant attribute selectors --}}
                @foreach ($attributeOptions as $option)
                    <div class="mt-6" data-attribute-group="{{ $option['id'] }}">
                        <p class="text-sm font-semibold text-slate-800">
                            {{ $option['name'] }}: <span class="font-normal text-slate-500" data-attribute-selected-label>{{ $option['values'][0]['value'] ?? '' }}</span>
                        </p>
                        <div class="flex flex-wrap gap-2.5 mt-2.5">
                            @foreach ($option['values'] as $index => $value)
                                <button type="button"
                                    class="web-variant-pill {{ $index === 0 ? 'is-active' : '' }}"
                                    data-attribute-id="{{ $option['id'] }}"
                                    data-value-id="{{ $value['id'] }}"
                                    data-value-label="{{ $value['value'] }}">
                                    {{ $value['value'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Quantity + Add to cart --}}
                <div class="flex flex-wrap items-center gap-3 mt-7">
                    <div class="web-qty-stepper">
                        <button type="button" id="qty-minus" aria-label="Decrease quantity"><i class="fa-solid fa-minus"></i></button>
                        <input id="qty-input" type="text" inputmode="numeric" value="1" aria-label="{{ __('website.quantity') }}">
                        <button type="button" id="qty-plus" aria-label="Increase quantity"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <div class="flex-1 min-w-[220px] flex gap-2">
                        <button type="button" id="add-to-cart-btn" class="web-btn-primary flex-1" {{ $stock > 0 ? '' : 'disabled' }}>
                            <i class="fa-solid fa-bag-shopping"></i> {{ __('website.add_to_cart') }}
                        </button>
                        <button type="button" id="buy-now-btn" class="web-btn-dark flex-1" {{ $stock > 0 ? '' : 'disabled' }}>
                            {{ __('website.buy_now') }}
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-5">
                    <button type="button" id="wishlist-btn"
                        class="web-icon-action-btn {{ $isWishlisted ? 'is-wishlisted' : '' }}"
                        data-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                        data-label-add="{{ __('website.remove_from_favorites') }}"
                        data-label-remove="{{ __('website.add_to_favorites') }}">
                        <i class="{{ $isWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart {{ $isWishlisted ? 'text-red-500' : '' }}"></i>
                        <span id="wishlist-btn-label">
                            {{ $isWishlisted ? __('website.remove_from_favorites') : __('website.add_to_favorites') }}
                        </span>
                    </button>
                    <button type="button" id="share-btn" class="web-icon-action-btn">
                        <i class="fa-solid fa-share-nodes"></i> {{ __('website.share') }}
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-7 pt-6 border-t border-slate-100">
                    <div class="flex flex-col items-center text-center gap-1.5">
                        <i class="fa-solid fa-truck-fast text-brand text-lg"></i>
                        <span class="text-[11px] text-slate-500 leading-tight">{{ __('website.free_shipping') }}</span>
                    </div>
                    <div class="flex flex-col items-center text-center gap-1.5">
                        <i class="fa-solid fa-rotate-left text-brand text-lg"></i>
                        <span class="text-[11px] text-slate-500 leading-tight">{{ __('website.easy_returns') }}</span>
                    </div>
                    <div class="flex flex-col items-center text-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-brand text-lg"></i>
                        <span class="text-[11px] text-slate-500 leading-tight">{{ __('website.secure_checkout') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ TABS ============ --}}
        <div class="mt-12 max-w-3xl">
            <div class="flex border-b border-slate-200" role="tablist">
                <button type="button" class="web-tab-btn is-active" data-tab="description">{{ __('website.description') }}</button>
                <button type="button" class="web-tab-btn" data-tab="specifications">{{ __('website.specifications') }}</button>
                <button type="button" class="web-tab-btn" data-tab="reviews">{{ __('website.reviews') }}</button>
            </div>

            <div data-tab-panel="description" class="py-5">
                @if ($product->description)
                    <div class="text-sm text-slate-600 leading-relaxed">{!! nl2br(e($product->description)) !!}</div>
                @else
                    <p class="text-sm text-slate-500">{{ __('website.no_description') }}</p>
                @endif
                <p class="text-xs text-slate-400 mt-4">{{ __('website.sku') }}: {{ $product->sku }}</p>
            </div>

            <div data-tab-panel="specifications" class="py-5 hidden">
                @if ($product->weight || $product->width || $product->height || $product->length)
                    <table class="w-full text-sm">
                        <tbody>
                            @if ($product->weight)
                                <tr class="border-b border-slate-100">
                                    <th class="text-start py-2 font-medium text-slate-700 w-1/3">{{ __('website.weight') }}</th>
                                    <td class="py-2 text-slate-600">{{ $product->weight }} kg</td>
                                </tr>
                            @endif
                            @if ($product->width && $product->height && $product->length)
                                <tr class="border-b border-slate-100">
                                    <th class="text-start py-2 font-medium text-slate-700">{{ __('website.dimensions') }}</th>
                                    <td class="py-2 text-slate-600">{{ $product->width }} x {{ $product->height }} x {{ $product->length }} cm</td>
                                </tr>
                            @endif
                            <tr class="border-b border-slate-100">
                                <th class="text-start py-2 font-medium text-slate-700">{{ __('website.sku') }}</th>
                                <td class="py-2 text-slate-600">{{ $product->sku }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-slate-500">{{ __('website.no_specifications') }}</p>
                @endif
            </div>

            <div data-tab-panel="reviews" class="py-5 hidden">
                @if ($product->reviews->isEmpty())
                    <p class="text-sm text-slate-500">{{ __('website.no_reviews_yet') }}</p>
                @else
                    <div class="space-y-5 mb-8">
                        @foreach ($product->reviews as $review)
                            <div class="border-b border-slate-100 pb-5">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-slate-800">{{ $review->customer->name ?? __('website.anonymous') }}</span>
                                    <span class="text-xs text-slate-400">{{ $review->created_at->format('Y-m-d') }}</span>
                                </div>
                                <x-website.rating :value="$review->rating" :count="0" class="mt-1" />
                                @if ($review->title)
                                    <p class="text-sm font-medium text-slate-800 mt-2">{{ $review->title }}</p>
                                @endif
                                @if ($review->body)
                                    <p class="text-sm text-slate-600 mt-1">{{ $review->body }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @auth('customer')
                    <div class="border-t border-slate-100 pt-5">
                        <h3 class="text-sm font-semibold text-slate-800 mb-3">{{ __('website.write_a_review') }}</h3>
                        <form method="POST" action="{{ route('web.products.reviews.store', ['lang' => current_lang(), 'product' => $product->id]) }}" class="space-y-3 max-w-lg">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('website.rating') }}</label>
                                <select name="rating" required class="w-full rounded-lg border-slate-300 text-sm">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}">{{ $i }} {{ __('website.stars') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('website.review_title') }}</label>
                                <input type="text" name="title" maxlength="255" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('website.review_body') }}</label>
                                <textarea name="body" rows="4" maxlength="2000" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-brand text-white text-sm font-medium">{{ __('website.submit_review') }}</button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-slate-100 pt-5">
                        <p class="text-sm text-slate-500">
                            {{ __('website.review_login_prompt') }}
                            <a href="{{ route('web.account.login', ['lang' => current_lang()]) }}" class="text-brand font-medium">{{ __('website.sign_in') }}</a>
                        </p>
                    </div>
                @endauth
            </div>
        </div>

        {{-- ============ RELATED PRODUCTS ============ --}}
        @if ($relatedProducts->isNotEmpty())
            <section class="mt-16">
                <div class="web-section-head">
                    <h2 class="text-xl font-bold text-slate-900">{{ __('website.related_products') }}</h2>
                    @if ($product->category)
                        <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $product->category->slug]) }}" class="web-view-all">
                            {{ __('website.view_all') }} <i class="fa-solid fa-chevron-{{ current_lang() === 'ar' ? 'left' : 'right' }} text-[10px]"></i>
                        </a>
                    @endif
                </div>
                <div class="swiper web-product-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($relatedProducts as $related)
                            <div class="swiper-slide">
                                <x-website.product-card :product="$related" class="h-full" />
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/product-show.js'])
@endpush
