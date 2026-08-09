@extends('website.layouts.app')

@section('title', __('website.shopping_cart'))

@section('content')
    <div class="web-cart max-w-7xl mx-auto px-4 py-8">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.shopping_cart'), 'url' => null],
        ]" />

        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ __('website.shopping_cart') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-6">

            {{-- ============ LEFT: ITEMS LIST ============ --}}
            <div class="lg:col-span-8">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-slate-500">
                        <span id="items-count" class="font-semibold text-slate-900">{{ count($items) }}</span>
                        {{ __('website.items_in_cart') }}
                    </p>
                    @if (count($items) > 0)
                        <button id="clear-cart-btn" type="button" class="text-xs font-semibold text-slate-400 hover:text-red-600 transition-colors">
                            <i class="fa-regular fa-trash-can me-1"></i>{{ __('website.clear_cart') }}
                        </button>
                    @endif
                </div>

                <div id="cart-items-list" class="flex flex-col gap-4 {{ count($items) === 0 ? 'hidden' : '' }}">
                    @foreach ($items as $item)
                        @php
                            $product = $item['product'];
                            $variant = $item['variant'];
                            $image = $product->main_image?->url ?? 'https://placehold.co/200x200';
                            $variantLabel = $variant?->variantAttributes
                                ->map(fn ($va) => $va->attributeValue?->value)
                                ->filter()
                                ->implode(' · ');
                        @endphp
                        <div class="web-cart-item" data-key="{{ $item['key'] }}" data-price="{{ $item['price'] }}">
                            <a href="{{ route('web.products.show', ['lang' => current_lang(), 'slug' => $product->slug]) }}" class="web-cart-item-img">
                                <img src="{{ $image }}" alt="{{ $product->name }}">
                            </a>
                            <div class="web-cart-item-info">
                                <a href="{{ route('web.products.show', ['lang' => current_lang(), 'slug' => $product->slug]) }}" class="web-cart-item-title">{{ $product->name }}</a>
                                @if ($variantLabel)
                                    <p class="web-cart-item-variant">{{ $variantLabel }}</p>
                                @endif
                                <button type="button" class="web-cart-item-remove" data-cart-remove data-key="{{ $item['key'] }}">
                                    <i class="fa-regular fa-trash-can"></i> {{ __('website.remove') }}
                                </button>
                            </div>
                            <div class="web-cart-item-qty">
                                <div class="web-qty-stepper">
                                    <button type="button" class="qty-minus" data-key="{{ $item['key'] }}" aria-label="Decrease quantity"><i class="fa-solid fa-minus"></i></button>
                                    <input class="qty-input" type="text" inputmode="numeric" value="{{ $item['qty'] }}" data-key="{{ $item['key'] }}" aria-label="Quantity">
                                    <button type="button" class="qty-plus" data-key="{{ $item['key'] }}" aria-label="Increase quantity"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="web-cart-item-price">
                                <span class="web-cart-line-total" data-line-total>{{ money_format($item['total']) }}</span>
                                <span class="web-cart-unit-price">{{ money_format($item['price']) }} {{ __('website.each') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty cart state --}}
                <div id="empty-cart-state" class="web-empty-state {{ count($items) > 0 ? 'hidden' : '' }}">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <p class="font-semibold text-lg mt-4 text-slate-700">{{ __('website.empty_cart_title') }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ __('website.empty_cart_subtitle') }}</p>
                    <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-btn-primary mt-5 inline-flex px-8">{{ __('website.continue_shopping') }}</a>
                </div>

                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 mt-6 hover:underline">
                    <i class="fa-solid fa-arrow-{{ current_lang() === 'ar' ? 'right' : 'left' }}"></i> {{ __('website.continue_shopping') }}
                </a>
            </div>

            {{-- ============ RIGHT: SUMMARY ============ --}}
            <div class="lg:col-span-4">
                <div class="web-summary-card">
                    <h2 class="font-bold text-lg mb-5">{{ __('website.order_summary') }}</h2>

                    <div class="web-summary-row">
                        <span>{{ __('website.subtotal') }}</span>
                        <span id="summary-subtotal">{{ money_format($subtotal) }}</span>
                    </div>
                    <div id="summary-discount-row" class="web-summary-row text-emerald-600 hidden">
                        <span>{{ __('website.discount') }} <span id="summary-discount-label"></span></span>
                        <span id="summary-discount">&minus;{{ money_format(0) }}</span>
                    </div>
                    <div class="web-summary-row">
                        <span>{{ __('website.shipping') }}</span>
                        <span id="summary-shipping">{{ __('website.free') }}</span>
                    </div>

                    <div class="web-summary-divider"></div>

                    <div class="web-summary-row web-summary-total">
                        <span>{{ __('website.total') }}</span>
                        <span id="summary-total">{{ money_format($subtotal) }}</span>
                    </div>

                    {{-- Coupon --}}
                    <div class="mt-5">
                        <label for="coupon-input" class="text-xs font-semibold text-slate-500">{{ __('website.have_promo_code') }}</label>
                        <div class="web-coupon-form mt-2">
                            <input id="coupon-input" type="text" placeholder="{{ __('website.enter_coupon_code') }}" autocomplete="off">
                            <button id="coupon-apply-btn" type="button">{{ __('website.apply') }}</button>
                        </div>
                        <div id="coupon-message" class="web-coupon-message hidden"></div>
                    </div>

                    <a href="{{ count($items) > 0 ? route('web.checkout.index', ['lang' => current_lang()]) : '#' }}"
                        id="checkout-btn"
                        class="web-btn-primary w-full mt-6 {{ count($items) === 0 ? 'pointer-events-none opacity-50' : '' }}">
                        <i class="fa-solid fa-lock"></i> {{ __('website.proceed_to_checkout') }}
                    </a>

                    <div class="flex items-center justify-center gap-2 text-xs text-slate-400 mt-4">
                        <i class="fa-solid fa-shield-halved"></i> {{ __('website.secure_checkout_note') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/cart.js'])
@endpush
