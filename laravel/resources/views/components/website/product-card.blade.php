@props(['product', 'priority' => false])

@php
    $cardImagePath = $product->main_image?->path;
    $cardImageUrl = $product->main_image?->url ?? 'https://placehold.co/300x300';
    $cardImageWebp = $cardImagePath ? webp_url($cardImagePath) : null;
@endphp

<div {{ $attributes->merge(['class' => 'group relative rounded-lg border border-slate-100 hover:shadow-lg transition-shadow overflow-hidden']) }}>
    @if ($product->is_on_sale)
        <x-website.badge text="{{ __('Sale') }}" color="red" class="absolute top-3 start-3 z-10" />
    @endif

    <button type="button"
        onclick="WebsiteApi.toggleWishlist({{ $product->id }}).then(() => this.classList.toggle('text-red-500'))"
        class="absolute top-3 end-3 z-10 w-8 h-8 rounded-full bg-white/90 flex items-center justify-center text-slate-400 hover:text-red-500"
        aria-label="{{ __('website.add_to_favorites') }} {{ $product->name }}">
        <i class="fa-regular fa-heart"></i>
    </button>

    <a href="{{ $product->slug ? route('web.products.show', ['lang' => current_lang(), 'slug' => $product->slug]) : '#' }}" class="block">
        <div class="relative">
            <picture>
                @if ($cardImageWebp)
                    <source srcset="{{ $cardImageWebp }}" type="image/webp">
                @endif
                <img src="{{ $cardImageUrl }}" alt="{{ $product->name }}"
                    class="w-full aspect-square object-cover" width="300" height="300"
                    @if ($priority)
                        loading="eager" fetchpriority="high"
                    @else
                        loading="lazy" decoding="async"
                    @endif
                >
            </picture>

            <button type="button"
                onclick="event.preventDefault(); WebsiteApi.addToCart({{ $product->id }})"
                class="absolute bottom-3 start-3 z-10 w-9 h-9 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600"
                title="{{ __('website.add_to_cart') }}"
                aria-label="{{ __('website.add_to_cart') }} {{ $product->name }}">
                <i class="fa-solid fa-cart-plus"></i>
            </button>
        </div>

        @if ($product->category)
            <p class="text-xs text-slate-400 px-3 mt-3">{{ $product->category->name }}</p>
        @endif
        <h3 class="text-sm font-medium text-slate-800 line-clamp-2 px-3 mt-1">{{ $product->name }}</h3>
    </a>

    <div class="px-3 mt-2 mb-3 flex items-center gap-2">
        <span class="font-semibold text-blue-600">{{ money_format($product->effective_price) }}</span>
        @if ($product->is_on_sale)
            <span class="text-xs text-slate-400 line-through">{{ money_format($product->price) }}</span>
        @endif
    </div>
</div>
