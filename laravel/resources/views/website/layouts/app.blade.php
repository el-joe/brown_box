<!DOCTYPE html>
<html lang="{{ current_lang() }}" @if(current_lang() === 'ar') dir="rtl" @endif x-data="{ mobileOpen: false, searchOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('website.site_name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @if(app()->environment('local') || file_exists(public_path('build/manifest.json')))
        @vite([is_rtl() ? 'resources/css/website/app.rtl.css' : 'resources/css/website/app.ltr.css', 'resources/js/website/app.js'])
    @endif
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

    {{-- Announcement bar --}}
    <div class="bg-slate-900 text-white text-xs">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-center text-center gap-2">
            <i class="fa-solid fa-bolt text-accent"></i>
            <span>{{ __('website.announcement_text') }} <a href="{{ route('web.faqs.index', ['lang' => current_lang()]) }}" class="underline font-semibold hover:text-accent">{{ __('website.learn_more') }}</a></span>
        </div>
    </div>

    {{-- Header --}}
    <header class="web-header sticky top-0 z-40 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between gap-4 py-4">
                {{-- Mobile menu button --}}
                <button type="button" @click="mobileOpen = true" class="lg:hidden text-xl text-slate-900" aria-label="{{ __('website.open_menu') }}">
                    <i class="fa-solid fa-bars"></i>
                </button>

                {{-- Logo --}}
                <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="flex items-center gap-2 shrink-0">
                    <x-website.logo class="text-xl sm:text-2xl" />
                </a>

                {{-- Search (desktop) --}}
                <form action="{{ route('web.search.index', ['lang' => current_lang()]) }}" method="GET" class="hidden lg:flex flex-1 max-w-xl mx-6">
                    <div class="flex w-full rounded-full border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-brand/40">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('website.search_placeholder') }}"
                            class="w-full px-5 py-2.5 text-sm outline-none">
                        <button type="submit" class="bg-brand hover:bg-brand-dark text-white px-5 flex items-center justify-center transition-colors" aria-label="{{ __('website.search') }}">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                {{-- Icons --}}
                <div class="flex items-center gap-1 sm:gap-2">
                    @php
                        $pathSegments = explode('/', trim(request()->path(), '/'));
                        array_shift($pathSegments);
                        $restOfPath = implode('/', $pathSegments);
                    @endphp
                    <div class="hidden sm:flex items-center gap-1 text-sm me-1">
                        <a href="{{ url('en'.($restOfPath ? '/'.$restOfPath : '')) }}" class="px-2 py-1 rounded {{ current_lang() === 'en' ? 'bg-brand-light text-brand-dark font-medium' : 'text-slate-500' }}">EN</a>
                        <a href="{{ url('ar'.($restOfPath ? '/'.$restOfPath : '')) }}" class="px-2 py-1 rounded {{ current_lang() === 'ar' ? 'bg-brand-light text-brand-dark font-medium' : 'text-slate-500' }}">AR</a>
                    </div>

                    <button type="button" @click="searchOpen = true" class="lg:hidden web-icon-btn" aria-label="{{ __('website.search') }}">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

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
                </div>
            </div>

            {{-- Main nav (desktop) --}}
            <nav class="hidden lg:flex items-center gap-7 border-t border-slate-100 py-3 text-sm font-medium">
                <div class="relative group">
                    <button type="button" class="flex items-center gap-2 bg-brand hover:bg-brand-dark text-white px-4 py-2 rounded-md transition-colors">
                        <i class="fa-solid fa-bars"></i> {{ __('website.shop_by_categories') }}
                    </button>
                    <div class="absolute start-0 top-full mt-2 w-64 bg-white border border-slate-100 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-30 py-2">
                        @foreach ($headerCategories as $category)
                            <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $category->slug]) }}"
                                class="flex items-center gap-3 px-4 py-2.5 hover:bg-brand-light hover:text-brand-dark">
                                <i class="fa-solid {{ $category->icon ?: 'fa-tag' }} w-4"></i> {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.home') }}</a>
                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.shop') }}</a>
                <a href="{{ route('web.blog.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.blog') }}</a>
                <a href="{{ route('web.track-order.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.track_order') }}</a>
                <a href="{{ route('web.contact.index', ['lang' => current_lang()]) }}" class="web-nav-link">{{ __('website.contact') }}</a>
            </nav>
        </div>
    </header>

    {{-- Search overlay (mobile) --}}
    <div x-show="searchOpen" x-cloak @click.self="searchOpen = false" class="fixed inset-0 z-50 bg-slate-900/50 lg:hidden">
        <form action="{{ route('web.search.index', ['lang' => current_lang()]) }}" method="GET" class="bg-white p-4 flex items-center gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('website.search_placeholder') }}"
                class="flex-1 border border-slate-200 rounded-full px-4 py-2.5 text-sm outline-none">
            <button type="button" @click="searchOpen = false" class="text-xl text-slate-900" aria-label="{{ __('website.close_search') }}"><i class="fa-solid fa-xmark"></i></button>
        </form>
    </div>

    {{-- Mobile drawer menu --}}
    <div x-show="mobileOpen" x-cloak @click="mobileOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"></div>
    <aside x-show="mobileOpen" x-cloak x-transition
        class="fixed top-0 {{ current_lang() === 'ar' ? 'right-0' : 'left-0' }} h-full w-80 max-w-[85vw] bg-white z-50 lg:hidden overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b border-slate-100">
            <x-website.logo class="text-xl" />
            <button type="button" @click="mobileOpen = false" class="text-xl" aria-label="{{ __('website.close_menu') }}"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <nav class="p-4 text-sm font-medium">
            <div x-data="{ open: false }">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between py-3 border-b border-slate-100">
                    {{ __('website.shop_by_categories') }} <i class="fa-solid fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ps-3 pb-2">
                    @foreach ($headerCategories as $category)
                        <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $category->slug]) }}" class="block py-2 text-slate-600">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="block py-3 border-b border-slate-100">{{ __('website.home') }}</a>
            <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="block py-3 border-b border-slate-100">{{ __('website.shop') }}</a>
            <a href="{{ route('web.blog.index', ['lang' => current_lang()]) }}" class="block py-3 border-b border-slate-100">{{ __('website.blog') }}</a>
            <a href="{{ route('web.track-order.index', ['lang' => current_lang()]) }}" class="block py-3 border-b border-slate-100">{{ __('website.track_order') }}</a>
            <a href="{{ route('web.contact.index', ['lang' => current_lang()]) }}" class="block py-3 border-b border-slate-100">{{ __('website.contact') }}</a>
            <div class="sm:hidden flex items-center gap-1 mt-4 text-sm">
                <a href="{{ url('en'.($restOfPath ? '/'.$restOfPath : '')) }}" class="px-2 py-1 rounded {{ current_lang() === 'en' ? 'bg-brand-light text-brand-dark font-medium' : 'text-slate-500' }}">EN</a>
                <a href="{{ url('ar'.($restOfPath ? '/'.$restOfPath : '')) }}" class="px-2 py-1 rounded {{ current_lang() === 'ar' ? 'bg-brand-light text-brand-dark font-medium' : 'text-slate-500' }}">AR</a>
            </div>
        </nav>
    </aside>

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
                    <x-website.logo dark class="text-xl" />
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
                        class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-brand">
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
