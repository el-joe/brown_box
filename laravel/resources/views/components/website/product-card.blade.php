@props(['product'])

<div {{ $attributes->merge(['class' => 'group relative rounded-lg border border-slate-100 hover:shadow-lg transition-shadow p-3']) }}>
    @if ($product->is_on_sale)
        <x-website.badge text="Sale" color="red" class="absolute top-3 start-3 z-10" />
    @endif

    <button type="button"
        onclick="WebsiteApi.toggleWishlist({{ $product->id }}).then(() => this.classList.toggle('text-red-500'))"
        class="absolute top-3 end-3 z-10 w-8 h-8 rounded-full bg-white/90 flex items-center justify-center text-slate-400 hover:text-red-500">
        <i class="fa-regular fa-heart"></i>
    </button>

    <a href="{{ route('web.products.show', ['lang' => current_lang(), 'slug' => $product->slug]) }}">
        <img src="{{ $product->main_image?->url ?? 'https://placehold.co/300x300' }}" alt="{{ $product->name }}"
            class="w-full aspect-square object-cover rounded-lg mb-3">
        <h3 class="text-sm font-medium text-slate-800 line-clamp-2">{{ $product->name }}</h3>
    </a>

    <div class="mt-2 flex items-center gap-2">
        <span class="font-semibold text-amber-600">{{ money_format($product->effective_price) }}</span>
        @if ($product->is_on_sale)
            <span class="text-xs text-slate-400 line-through">{{ money_format($product->price) }}</span>
        @endif
    </div>

    <button type="button"
        onclick="WebsiteApi.addToCart({{ $product->id }})"
        class="web-btn-primary w-full mt-3 !py-1.5 text-xs">
        {{ __('website.add_to_cart') }}
    </button>
</div>
