@php
    $customer = $customer ?? auth('customer')->user();
    $active = $active ?? null;
@endphp

<aside class="web-account-sidebar">
    <div class="web-account-user">
        <div class="web-account-avatar">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</div>
        <div class="min-w-0">
            <p class="font-bold text-sm text-slate-900 truncate">{{ $customer->name }}</p>
            <p class="text-xs text-slate-500 truncate">{{ $customer->email }}</p>
        </div>
    </div>
    <nav class="web-account-nav">
        <a href="{{ route('web.account.orders', ['lang' => current_lang()]) }}" class="{{ $active === 'orders' ? 'is-active' : '' }}">
            <i class="fa-solid fa-box"></i> {{ __('website.my_orders') }}
        </a>
        <a href="{{ route('web.account.wishlist', ['lang' => current_lang()]) }}" class="{{ $active === 'wishlist' ? 'is-active' : '' }}">
            <i class="fa-regular fa-heart"></i> {{ __('website.wishlist') }}
        </a>
        <a href="{{ route('web.account.profile', ['lang' => current_lang()]) }}" class="{{ $active === 'profile' ? 'is-active' : '' }}">
            <i class="fa-regular fa-user"></i> {{ __('website.edit_profile') }}
        </a>
        <a href="{{ route('web.account.addresses.index', ['lang' => current_lang()]) }}" class="{{ $active === 'addresses' ? 'is-active' : '' }}">
            <i class="fa-solid fa-location-dot"></i> {{ __('website.addresses') }}
        </a>
        <form action="{{ route('web.account.logout', ['lang' => current_lang()]) }}" method="POST">
            @csrf
            <button type="submit" class="web-account-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> {{ __('website.logout') }}</button>
        </form>
    </nav>
</aside>
