<!DOCTYPE html>
<html lang="{{ current_lang() }}" @if(current_lang() === 'ar') dir="rtl" @endif x-data="{ mobileOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('website.site_name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/website/app.css', 'resources/js/website/app.js'])
    @stack('styles')
</head>
<body class="bg-white text-slate-800 antialiased">
    <script>
        window.routes = {
            cartAdd: @js(route('web.cart.add', ['lang' => current_lang()])),
            cartUpdate: @js(route('web.cart.update', ['lang' => current_lang(), 'key' => '__KEY__'])),
            cartRemove: @js(route('web.cart.remove', ['lang' => current_lang(), 'key' => '__KEY__'])),
            cartClear: @js(route('web.cart.clear', ['lang' => current_lang()])),
            wishlistToggle: @js(route('web.wishlist.toggle', ['lang' => current_lang()])),
            searchSuggestions: @js(route('web.search.suggestions', ['lang' => current_lang()])),
            couponApply: @js(route('web.coupon.apply', ['lang' => current_lang()])),
            shippingCompanies: @js(route('web.shipping.companies', ['lang' => current_lang()])),
            checkoutStore: @js(route('web.checkout.store', ['lang' => current_lang()])),
            paymentUploadProof: @js(route('web.payment.upload-proof', ['lang' => current_lang(), 'order' => '__ORDER__'])),
        };
    </script>

    {{-- Header --}}
    <header class="web-header sticky top-0 z-30 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16 gap-4">
                {{-- Logo --}}
                <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-box text-amber-600 text-xl"></i>
                    <span class="font-bold text-lg text-slate-900">{{ __('website.site_name') }}</span>
                </a>

                {{-- Nav (desktop) --}}
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.home') }}</a>
                    <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.shop') }}</a>
                    <a href="{{ route('web.blog.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.blog') }}</a>
                    <a href="{{ route('web.track-order.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.track_order') }}</a>
                    <a href="{{ route('web.contact.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.contact') }}</a>
                </nav>

                {{-- Search --}}
                <form action="{{ route('web.search.index', ['lang' => current_lang()]) }}" method="GET" class="hidden lg:flex flex-1 max-w-md">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('website.search_placeholder') }}"
                        class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </form>

                {{-- Actions --}}
                <div class="flex items-center gap-1">
                    {{-- Lang switcher --}}
                    @php
                        $pathSegments = explode('/', trim(request()->path(), '/'));
                        array_shift($pathSegments);
                        $restOfPath = implode('/', $pathSegments);
                    @endphp
                    <div class="hidden sm:flex items-center gap-1 text-sm me-2">
                        <a href="{{ url('en'.($restOfPath ? '/'.$restOfPath : '')) }}" class="px-2 py-1 rounded {{ current_lang() === 'en' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500' }}">EN</a>
                        <a href="{{ url('ar'.($restOfPath ? '/'.$restOfPath : '')) }}" class="px-2 py-1 rounded {{ current_lang() === 'ar' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500' }}">AR</a>
                    </div>

                    <a href="{{ route('web.wishlist.index', ['lang' => current_lang()]) }}" class="web-icon-btn" title="{{ __('website.wishlist') }}">
                        <i class="fa-regular fa-heart"></i>
                    </a>

                    <a href="{{ route('web.cart.index', ['lang' => current_lang()]) }}" class="web-icon-btn" title="{{ __('website.cart') }}">
                        <i class="fa-solid fa-cart-shopping"></i>
                        @php($cartCount = collect(session('cart', []))->sum('qty'))
                        @if($cartCount > 0)
                            <span class="web-badge-count">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @auth('customer')
                        <a href="{{ route('web.account.index', ['lang' => current_lang()]) }}" class="web-icon-btn" title="{{ __('website.account') }}">
                            <i class="fa-regular fa-user"></i>
                        </a>
                    @else
                        <a href="{{ route('web.account.login', ['lang' => current_lang()]) }}" class="web-icon-btn" title="{{ __('website.login') }}">
                            <i class="fa-regular fa-user"></i>
                        </a>
                    @endauth

                    {{-- Mobile menu toggle --}}
                    <button type="button" data-mobile-menu-toggle class="md:hidden web-icon-btn">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div data-mobile-menu class="hidden md:hidden border-t border-slate-100 px-4 py-3 space-y-3">
            <form action="{{ route('web.search.index', ['lang' => current_lang()]) }}" method="GET">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('website.search_placeholder') }}"
                    class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </form>
            <nav class="flex flex-col gap-2">
                <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.home') }}</a>
                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.shop') }}</a>
                <a href="{{ route('web.blog.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.blog') }}</a>
                <a href="{{ route('web.track-order.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.track_order') }}</a>
                <a href="{{ route('web.contact.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.contact') }}</a>
            </nav>
        </div>
    </header>

    {{-- Flash messages --}}
    @if (session('success') || session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            @if (session('success'))
                <div class="rounded-lg bg-emerald-50 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-lg bg-red-50 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
            @endif
        </div>
    @endif

    {{-- Content --}}
    <main class="min-h-[60vh]">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <i class="fa-solid fa-box text-amber-500 text-xl"></i>
                    <span class="font-bold text-lg text-white">{{ __('website.site_name') }}</span>
                </div>
                <p class="text-sm text-slate-400">{{ __('website.footer_about') }}</p>
                <div class="flex items-center gap-3 mt-4">
                    <a href="#" class="web-icon-btn !text-slate-300 hover:!text-white"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="web-icon-btn !text-slate-300 hover:!text-white"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="web-icon-btn !text-slate-300 hover:!text-white"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">{{ __('website.footer_help') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('web.track-order.index', ['lang' => current_lang()]) }}" class="hover:text-white">{{ __('website.track_order') }}</a></li>
                    <li><a href="{{ route('web.faqs.index', ['lang' => current_lang()]) }}" class="hover:text-white">{{ __('website.faqs') }}</a></li>
                    <li><a href="{{ route('web.contact.index', ['lang' => current_lang()]) }}" class="hover:text-white">{{ __('website.contact') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">{{ __('website.footer_policies') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('web.pages.show', ['lang' => current_lang(), 'slug' => 'privacy-policy']) }}" class="hover:text-white">{{ __('website.privacy_policy') }}</a></li>
                    <li><a href="{{ route('web.pages.show', ['lang' => current_lang(), 'slug' => 'terms-conditions']) }}" class="hover:text-white">{{ __('website.terms_conditions') }}</a></li>
                    <li><a href="{{ route('web.pages.show', ['lang' => current_lang(), 'slug' => 'shipping-policy']) }}" class="hover:text-white">{{ __('website.shipping_policy') }}</a></li>
                    <li><a href="{{ route('web.pages.show', ['lang' => current_lang(), 'slug' => 'refund-policy']) }}" class="hover:text-white">{{ __('website.refund_policy') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">{{ __('website.newsletter_title') }}</h4>
                <form class="flex gap-2">
                    <input type="email" placeholder="{{ __('website.newsletter_placeholder') }}"
                        class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="web-btn-primary shrink-0">{{ __('website.newsletter_subscribe') }}</button>
                </form>
                <p class="text-xs text-slate-500 mt-4">{{ __('website.payment_methods') }}</p>
                <div class="flex items-center gap-2 mt-2 text-xl text-slate-500">
                    <i class="fa-brands fa-cc-visa"></i>
                    <i class="fa-brands fa-cc-mastercard"></i>
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 py-4 text-center text-xs text-slate-500">
            &copy; {{ now()->year }} {{ __('website.site_name') }}. {{ __('website.all_rights_reserved') }}
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
