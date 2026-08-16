@props(['product'])

<div {{ $attributes->merge(['class' => 'group relative rounded-lg border border-slate-100 hover:shadow-lg transition-shadow overflow-hidden']) }}>
    @if ($product->is_on_sale)
        <x-website.badge text="Sale" color="red" class="absolute top-3 start-3 z-10" />
    @endif

    <button type="button"
        onclick="WebsiteApi.toggleWishlist({{ $product->id }}).then(() => this.classList.toggle('text-red-500'))"
        class="absolute top-3 end-3 z-10 w-8 h-8 rounded-full bg-white/90 flex items-center justify-center text-slate-400 hover:text-red-500">
        <i class="fa-regular fa-heart"></i>
    </button>

    <a href="{{ route('web.products.show', ['lang' => current_lang(), 'slug' => $product->slug]) }}" class="relative block">
        <img src="{{ $product->main_image?->url ?? 'https://placehold.co/300x300' }}" alt="{{ $product->name }}"
            class="w-full aspect-square object-cover">

        <button type="button"
            onclick="event.preventDefault(); WebsiteApi.addToCart({{ $product->id }})"
            class="absolute bottom-3 start-3 z-10 w-9 h-9 rounded-full bg-amber-500 text-white flex items-center justify-center hover:bg-amber-600"
            title="{{ __('website.add_to_cart') }}">
            <i class="fa-solid fa-cart-plus"></i>
        </button>

        @if ($product->category)
            <p class="text-xs text-slate-400 px-3 mt-3">{{ $product->category->name }}</p>
        @endif
        <h3 class="text-sm font-medium text-slate-800 line-clamp-2 px-3 mt-1">{{ $product->name }}</h3>
    </a>

    <div class="px-3 mt-2 mb-3 flex items-center gap-2">
        <span class="font-semibold text-amber-600">{{ money_format($product->effective_price) }}</span>
        @if ($product->is_on_sale)
            <span class="text-xs text-slate-400 line-through">{{ money_format($product->price) }}</span>
        @endif
    </div>
</div>
