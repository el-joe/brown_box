@extends('website.layouts.app')

@section('title', __('website.wishlist'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.wishlist'), 'url' => null],
        ]" />
    </div>

    @auth('customer')
        <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
            <div class="web-account-layout">
                @include('website.account._sidebar', ['active' => 'wishlist'])

                <div class="min-w-0">
                    <div class="web-account-card">
                        <div class="web-account-card-head">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">{{ __('website.wishlist') }}</h1>
                                <p class="web-account-card-sub">{{ __('website.no_wishlist_items_subtitle') }}</p>
                            </div>
                        </div>

                        @if ($items->isNotEmpty())
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                                @foreach ($items as $item)
                                    @if ($item->product)
                                        <div class="relative">
                                            <form method="POST" action="{{ route('web.wishlist.toggle', ['lang' => current_lang()]) }}"
                                                onsubmit="event.preventDefault(); WebsiteApi.toggleWishlist({{ $item->product->id }}).then(() => this.closest('.relative').remove());"
                                                class="absolute top-3 end-3 z-20">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center text-slate-500 hover:text-red-500"
                                                    aria-label="{{ __('website.wishlist') }}">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                            <x-website.product-card :product="$item->product" />
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="web-account-empty">
                                <i class="fa-regular fa-heart"></i>
                                <p class="font-semibold text-slate-600">{{ __('website.no_wishlist_items') }}</p>
                                <p class="text-sm text-slate-400 mt-1">{{ __('website.no_wishlist_items_subtitle') }}</p>
                                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-btn-primary mt-4">{{ __('website.browse_products') }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="max-w-7xl mx-auto px-4 py-10">
            <x-website.section-title :title="__('website.wishlist')" />

            @if ($items->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                    @foreach ($items as $item)
                        @if ($item->product)
                            <div class="relative">
                                <form method="POST" action="{{ route('web.wishlist.toggle', ['lang' => current_lang()]) }}"
                                    onsubmit="event.preventDefault(); WebsiteApi.toggleWishlist({{ $item->product->id }}).then(() => this.closest('.relative').remove());"
                                    class="absolute top-3 end-3 z-20">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center text-slate-500 hover:text-red-500"
                                        aria-label="{{ __('website.wishlist') }}">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                                <x-website.product-card :product="$item->product" />
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="web-account-empty">
                    <i class="fa-regular fa-heart"></i>
                    <p class="font-semibold text-slate-600">{{ __('website.no_wishlist_items') }}</p>
                    <p class="text-sm text-slate-400 mt-1">{{ __('website.no_wishlist_items_subtitle') }}</p>
                    <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-btn-primary mt-4">{{ __('website.browse_products') }}</a>
                </div>
            @endif
        </div>
    @endauth
@endsection
