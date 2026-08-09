@extends('website.layouts.app')

@section('title', $product->name)

@section('content')
    @php
        $mainImage = $product->main_image?->url ?? 'https://placehold.co/600x600';
        $gallery = $product->images->isNotEmpty() ? $product->images : collect();
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
            <div>
                <div class="relative rounded-xl overflow-hidden border border-slate-100">
                    @if ($hasDiscount)
                        <span class="absolute top-3 start-3 z-10 web-deal-badge">-{{ $discountPercent }}%</span>
                    @endif
                    <img id="product-main-image" src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover">
                </div>

                @if ($gallery->count() > 1)
                    <div class="flex gap-3 mt-3 overflow-x-auto pb-1">
                        @foreach ($gallery as $image)
                            <button type="button" data-thumb data-full="{{ $image->url }}"
                                class="web-gallery-thumb {{ $loop->first ? 'is-active' : '' }} shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 {{ $loop->first ? 'border-amber-500' : 'border-transparent' }}">
                                <img src="{{ $image->url }}" alt="{{ $product->name }} thumbnail" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ============ DETAILS ============ --}}
            <div>
                @if ($product->brand)
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide">{{ $product->brand->name }}</p>
                @endif
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $product->name }}</h1>

                <div class="flex items-center gap-3 mt-2.5">
                    <x-website.rating :value="0" :count="0" />
                    <span class="text-slate-300">|</span>
                    @if ($stock > 0)
                        <span class="text-xs text-emerald-600 font-medium"><i class="fa-solid fa-circle-check me-1"></i>{{ __('website.in_stock') }}</span>
                    @else
                        <span class="text-xs text-red-500 font-medium"><i class="fa-solid fa-circle-xmark me-1"></i>{{ __('website.out_of_stock') }}</span>
                    @endif
                </div>

                <div class="flex items-end gap-3 mt-4">
                    <span id="product-price-current" class="text-3xl font-extrabold {{ $hasDiscount ? 'text-red-600' : 'text-amber-600' }}">
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
                    <button type="button" id="wishlist-btn" class="web-icon-action-btn">
                        <i class="fa-regular fa-heart"></i> {{ __('website.add_to_favorites') }}
                    </button>
                    <button type="button" id="share-btn" class="web-icon-action-btn">
                        <i class="fa-solid fa-share-nodes"></i> {{ __('website.share') }}
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-7 pt-6 border-t border-slate-100">
                    <div class="flex flex-col items-center text-center gap-1.5">
                        <i class="fa-solid fa-truck-fast text-amber-600 text-lg"></i>
                        <span class="text-[11px] text-slate-500 leading-tight">{{ __('website.free_shipping') }}</span>
                    </div>
                    <div class="flex flex-col items-center text-center gap-1.5">
                        <i class="fa-solid fa-rotate-left text-amber-600 text-lg"></i>
                        <span class="text-[11px] text-slate-500 leading-tight">{{ __('website.easy_returns') }}</span>
                    </div>
                    <div class="flex flex-col items-center text-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-amber-600 text-lg"></i>
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
                <p class="text-sm text-slate-500">{{ __('website.no_reviews_yet') }}</p>
            </div>
        </div>

        {{-- ============ RELATED PRODUCTS ============ --}}
        @if ($relatedProducts->isNotEmpty())
            <section class="mt-16">
                <x-website.section-title :title="__('website.related_products')" />
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $related)
                        <x-website.product-card :product="$related" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/product-show.js'])
@endpush
