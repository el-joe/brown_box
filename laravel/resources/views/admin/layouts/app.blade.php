<!DOCTYPE html>
<html lang="{{ current_lang() }}" @if(current_lang() === 'ar') dir="rtl" @endif x-data="{ sidebarOpen: true }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) — Brown Box Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @if(app()->environment('local') || file_exists(public_path('build/manifest.json')))
        @vite([is_rtl() ? 'resources/css/admin/app.rtl.css' : 'resources/css/admin/app.ltr.css', 'resources/js/admin/app.js'])
    @endif
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col" :class="sidebarOpen ? '' : 'w-16'">
            <div class="h-16 flex items-center px-4 border-b border-slate-800">
                <span class="font-bold text-lg text-white" x-show="sidebarOpen">Brown Box</span>
                <i class="fa-solid fa-box text-amber-500 text-xl" x-show="!sidebarOpen"></i>
            </div>

            <nav class="flex-1 overflow-y-auto py-3 space-y-1" x-data="{ open: {{ Js::from([]) }} }">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high w-5"></i> <span x-show="sidebarOpen">{{ __('Dashboard') }}</span>
                </a>

                @php
                    $sections = [
                        'catalog' => ['icon' => 'fa-tags', 'label' => 'Catalog', 'items' => ['Categories' => 'fa-sitemap', 'Brands' => 'fa-copyright', 'Products' => 'fa-box-open', 'Attributes' => 'fa-sliders', 'Reviews' => 'fa-star']],
                        'inventory' => ['icon' => 'fa-warehouse', 'label' => 'Inventory', 'items' => ['Warehouses' => 'fa-industry', 'Stock' => 'fa-boxes-stacked', 'Purchases' => 'fa-cart-shopping', 'Suppliers' => 'fa-truck-field'],],
                        'sales' => ['icon' => 'fa-receipt', 'label' => 'Sales', 'items' => ['Orders' => 'fa-cart-flatbed', 'Refunds' => 'fa-rotate-left']],
                        'customers' => ['icon' => 'fa-users', 'label' => 'Customers', 'items' => []],
                        'marketing' => ['icon' => 'fa-bullhorn', 'label' => 'Marketing', 'items' => ['Banners' => 'fa-image', 'Coupons' => 'fa-ticket', 'Flash Sales' => 'fa-bolt', 'Search Suggestions' => 'fa-magnifying-glass', 'SEO' => 'fa-magnifying-glass-chart', 'Blog' => 'fa-newspaper', 'Blog Categories' => 'fa-sitemap']],
                        'ai' => ['icon' => 'fa-wand-magic-sparkles', 'label' => 'AI Module', 'items' => ['AI Dashboard' => 'fa-wand-magic-sparkles', 'SEO Enhancement' => 'fa-magnifying-glass-chart', 'Blog Generator' => 'fa-newspaper', 'Trending Research' => 'fa-chart-line', 'Social Posts' => 'fa-share-nodes', 'Product Descriptions' => 'fa-file-pen', 'AI Settings' => 'fa-gear']],
                        'affiliates' => ['icon' => 'fa-handshake', 'label' => 'Affiliates', 'items' => ['Affiliates' => 'fa-user-tie', 'Payout Requests' => 'fa-money-check-dollar']],
                        'finance' => ['icon' => 'fa-sack-dollar', 'label' => 'Finance', 'items' => ['Expenses' => 'fa-money-bill-wave', 'Expense Categories' => 'fa-sitemap', 'Accounting' => 'fa-book', 'Opening Balances' => 'fa-scale-balanced', 'Transactions' => 'fa-right-left']],
                        'pos' => ['icon' => 'fa-cash-register', 'label' => 'POS', 'items' => []],
                        'reports' => ['icon' => 'fa-chart-line', 'label' => 'Reports', 'items' => []],
                        'settings' => ['icon' => 'fa-gear', 'label' => 'Settings', 'items' => ['General' => 'fa-sliders', 'Gateways' => 'fa-credit-card', 'Shipping' => 'fa-truck', 'Admins' => 'fa-user-shield', 'Roles' => 'fa-key', 'Audit Log' => 'fa-clock-rotate-left', 'Static Pages' => 'fa-file-lines']],
                    ];
                @endphp

                @foreach ($sections as $key => $section)
                    @if (empty($section['items']))
                        @php
                            $directUrl = match ($key) {
                                'pos' => route('admin.orders.pos'),
                                'customers' => route('admin.customers.index'),
                                'reports' => route('admin.reports.index'),
                                default => '#',
                            };
                            $directActive = match ($key) {
                                'pos' => request()->routeIs('admin.orders.pos'),
                                'customers' => request()->routeIs('admin.customers.*'),
                                'reports' => request()->routeIs('admin.reports.*'),
                                default => false,
                            };
                        @endphp
                        <a href="{{ $directUrl }}" class="admin-nav-link {{ $directActive ? 'active' : '' }}">
                            <i class="fa-solid {{ $section['icon'] }} w-5"></i> <span x-show="sidebarOpen">{{ __($section['label']) }}</span>
                        </a>
                    @else
                        @php
                            $sectionRoutePatterns = [
                                'catalog' => ['admin.categories.*', 'admin.brands.*', 'admin.products.*', 'admin.attributes.*', 'admin.reviews.*'],
                                'inventory' => ['admin.warehouses.*', 'admin.stock.*', 'admin.purchases.*', 'admin.suppliers.*'],
                                'sales' => ['admin.orders.index', 'admin.orders.show', 'admin.refunds.*'],
                                'marketing' => ['admin.banners.*', 'admin.coupons.*', 'admin.flash-sales.*', 'admin.search-suggestions.*', 'admin.seo.*', 'admin.blog.*', 'admin.blog-categories.*'],
                                'affiliates' => ['admin.affiliates.*'],
                                'finance' => ['admin.expenses.*', 'admin.expense-categories.*', 'admin.accounting.*', 'admin.opening-balances.*', 'admin.transactions.*'],
                                'settings' => ['admin.settings.*', 'admin.gateways.*', 'admin.shipping.*', 'admin.admins.*', 'admin.roles.*', 'admin.audits.*', 'admin.static-pages.*'],
                                'ai' => ['admin.ai.*'],
                            ];
                            $sectionActive = request()->routeIs(...($sectionRoutePatterns[$key] ?? []));
                        @endphp
                        <div x-data="{ expanded: {{ $sectionActive ? 'true' : 'false' }} }">
                            <button type="button" @click="expanded = !expanded" class="admin-nav-link w-full justify-between {{ $sectionActive ? 'active' : '' }}">
                                <span class="flex items-center gap-3">
                                    <i class="fa-solid {{ $section['icon'] }} w-5"></i> <span x-show="sidebarOpen">{{ __($section['label']) }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="expanded ? 'rotate-180' : ''" x-show="sidebarOpen"></i>
                            </button>
                            <div x-show="expanded && sidebarOpen" x-collapse class="ps-10 pe-2 space-y-1">
                                @foreach ($section['items'] as $label => $icon)
                                    @php
                                        $navUrl = match ($label) {
                                            'Categories' => route('admin.categories.index'),
                                            'Brands' => route('admin.brands.index'),
                                            'Banners' => route('admin.banners.index'),
                                            'Attributes' => route('admin.attributes.index'),
                                            'Reviews' => route('admin.reviews.index'),
                                            'Products' => route('admin.products.index'),
                                            'Warehouses' => route('admin.warehouses.index'),
                                            'Stock' => route('admin.stock.index'),
                                            'Purchases' => route('admin.purchases.index'),
                                            'Suppliers' => route('admin.suppliers.index'),
                                            'Coupons' => route('admin.coupons.index'),
                                            'Flash Sales' => route('admin.flash-sales.index'),
                                            'Orders' => route('admin.orders.index'),
                                            'Refunds' => route('admin.refunds.index'),
                                            'Affiliates' => route('admin.affiliates.index'),
                                            'Payout Requests' => route('admin.affiliates.payouts.index'),
                                            'Expenses' => route('admin.expenses.index'),
                                            'Expense Categories' => route('admin.expense-categories.index'),
                                            'Accounting' => route('admin.accounting.index'),
                                            'Opening Balances' => route('admin.opening-balances.index'),
                                            'Transactions' => route('admin.transactions.index'),
                                            'General' => route('admin.settings.general'),
                                            'Gateways' => route('admin.gateways.index'),
                                            'Shipping' => route('admin.shipping.index'),
                                            'Admins' => route('admin.admins.index'),
                                            'Roles' => route('admin.roles.index'),
                                            'Audit Log' => route('admin.audits.index'),
                                            'Static Pages' => route('admin.static-pages.index'),
                                            'SEO' => route('admin.seo.index'),
                                            'Search Suggestions' => route('admin.search-suggestions.index'),
                                            'Blog' => route('admin.blog.index'),
                                            'Blog Categories' => route('admin.blog-categories.index'),
                                            'AI Dashboard' => route('admin.ai.dashboard'),
                                            'SEO Enhancement' => route('admin.ai.seo'),
                                            'Blog Generator' => route('admin.ai.blog'),
                                            'Trending Research' => route('admin.ai.trending'),
                                            'Social Posts' => route('admin.ai.social'),
                                            'Product Descriptions' => route('admin.ai.product-description'),
                                            'AI Settings' => route('admin.ai.settings'),
                                            default => '#',
                                        };
                                        $navActive = match ($label) {
                                            'Categories' => request()->routeIs('admin.categories.*'),
                                            'Brands' => request()->routeIs('admin.brands.*'),
                                            'Banners' => request()->routeIs('admin.banners.*'),
                                            'Attributes' => request()->routeIs('admin.attributes.*'),
                                            'Reviews' => request()->routeIs('admin.reviews.*'),
                                            'Products' => request()->routeIs('admin.products.*'),
                                            'Warehouses' => request()->routeIs('admin.warehouses.*'),
                                            'Stock' => request()->routeIs('admin.stock.*'),
                                            'Purchases' => request()->routeIs('admin.purchases.*'),
                                            'Suppliers' => request()->routeIs('admin.suppliers.*'),
                                            'Coupons' => request()->routeIs('admin.coupons.*'),
                                            'Flash Sales' => request()->routeIs('admin.flash-sales.*'),
                                            'Orders' => request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show'),
                                            'Refunds' => request()->routeIs('admin.refunds.*'),
                                            'Affiliates' => request()->routeIs('admin.affiliates.*') && ! request()->routeIs('admin.affiliates.payouts.*'),
                                            'Payout Requests' => request()->routeIs('admin.affiliates.payouts.*'),
                                            'Expenses' => request()->routeIs('admin.expenses.*'),
                                            'Expense Categories' => request()->routeIs('admin.expense-categories.*'),
                                            'Accounting' => request()->routeIs('admin.accounting.*'),
                                            'Opening Balances' => request()->routeIs('admin.opening-balances.*'),
                                            'Transactions' => request()->routeIs('admin.transactions.*'),
                                            'General' => request()->routeIs('admin.settings.*'),
                                            'Gateways' => request()->routeIs('admin.gateways.*'),
                                            'Shipping' => request()->routeIs('admin.shipping.*'),
                                            'Admins' => request()->routeIs('admin.admins.*'),
                                            'Roles' => request()->routeIs('admin.roles.*'),
                                            'Audit Log' => request()->routeIs('admin.audits.*'),
                                            'Static Pages' => request()->routeIs('admin.static-pages.*'),
                                            'SEO' => request()->routeIs('admin.seo.*'),
                                            'Search Suggestions' => request()->routeIs('admin.search-suggestions.*'),
                                            'Blog' => request()->routeIs('admin.blog.*'),
                                            'Blog Categories' => request()->routeIs('admin.blog-categories.*'),
                                            'AI Dashboard' => request()->routeIs('admin.ai.dashboard'),
                                            'SEO Enhancement' => request()->routeIs('admin.ai.seo'),
                                            'Blog Generator' => request()->routeIs('admin.ai.blog'),
                                            'Trending Research' => request()->routeIs('admin.ai.trending'),
                                            'Social Posts' => request()->routeIs('admin.ai.social'),
                                            'Product Descriptions' => request()->routeIs('admin.ai.product-description'),
                                            'AI Settings' => request()->routeIs('admin.ai.settings'),
                                            default => false,
                                        };
                                    @endphp
                                    <a href="{{ $navUrl }}"
                                        class="block py-1.5 text-sm {{ $navActive ? 'text-white' : 'text-slate-400 hover:text-white' }}">
                                        <i class="fa-solid {{ $icon }} me-2 text-xs"></i>{{ __($label) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-800">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <nav class="text-sm text-slate-500">
                        @yield('breadcrumb')
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Language switcher --}}
                    <div class="flex items-center gap-1 text-sm">
                        <a href="{{ route('admin.lang', 'en') }}" class="px-2 py-1 rounded {{ current_lang() === 'en' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500' }}">EN</a>
                        <a href="{{ route('admin.lang', 'ar') }}" class="px-2 py-1 rounded {{ current_lang() === 'ar' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500' }}">AR</a>
                    </div>

                    {{-- Notification bell --}}
                    <div class="relative"
                        x-data="{
                            open: false,
                            count: 0,
                            notifications: [],
                            fetchNotifications() {
                                fetch('{{ route('admin.notifications.index') }}', {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.count = data.count;
                                        this.notifications = data.notifications;
                                    });
                            },
                            markAllRead() {
                                fetch('{{ route('admin.notifications.readAll') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    },
                                })
                                    .then(() => {
                                        this.count = 0;
                                        this.notifications = [];
                                    });
                            },
                        }"
                        x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 60000)">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="relative text-slate-500 hover:text-slate-800">
                            <i class="fa-solid fa-bell text-lg"></i>
                            <span x-show="count > 0" x-cloak x-text="count > 99 ? '99+' : count"
                                class="absolute -top-1.5 -end-1.5 bg-red-600 text-white text-[10px] leading-none rounded-full px-1.5 py-1"></span>
                        </button>
                        <div x-show="open" x-cloak class="absolute end-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-slate-200 text-sm">
                            <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100">
                                <span class="font-medium text-slate-700">{{ __('Notifications') }}</span>
                                <button type="button" @click="markAllRead()" class="text-xs text-amber-600 hover:underline">{{ __('Mark all read') }}</button>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-6 text-center text-slate-400">{{ __('No new notifications') }}</div>
                                </template>
                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="px-4 py-2.5 border-b border-slate-50 last:border-0">
                                        <div class="font-medium text-slate-700" x-text="notification.data.title"></div>
                                        <div class="text-xs text-slate-400" x-text="notification.created_at"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Admin dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-2">
                            <img src="{{ asset_url(auth('admin')->user()?->avatar) ?? 'https://ui-avatars.com/api/?name='.urlencode(auth('admin')->user()->name ?? 'Admin') }}"
                                class="w-9 h-9 rounded-full object-cover border border-slate-200" alt="avatar">
                            <span class="text-sm font-medium text-slate-700">{{ auth('admin')->user()->name ?? '' }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>
                        <div x-show="open" x-cloak class="absolute end-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 text-sm">
                            <a href="#" class="block px-4 py-2 hover:bg-slate-50"><i class="fa-solid fa-user me-2"></i>{{ __('Profile') }}</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-start px-4 py-2 hover:bg-slate-50 text-red-600">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>{{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            <div x-data="{ toasts: [
                    @if (session('success')) { type: 'success', message: @js(session('success')) }, @endif
                    @if (session('error')) { type: 'error', message: @js(session('error')) }, @endif
                    @if (session('warning')) { type: 'warning', message: @js(session('warning')) }, @endif
                ] }" x-show="toasts.length" class="fixed top-4 end-4 z-50 space-y-2" style="min-width: 280px">
                <template x-for="(toast, index) in toasts" :key="index">
                    <div x-show="true" x-init="setTimeout(() => toasts.splice(index, 1), 4000)"
                        class="rounded-lg shadow-lg px-4 py-3 text-sm text-white flex items-center justify-between gap-3"
                        :class="{ 'bg-emerald-600': toast.type === 'success', 'bg-red-600': toast.type === 'error', 'bg-amber-500': toast.type === 'warning' }">
                        <span x-text="toast.message"></span>
                        <button @click="toasts.splice(index, 1)"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </template>
            </div>

            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <x-admin.confirm-delete />

    @if(app()->environment('local') || file_exists(public_path('build/manifest.json')))
        @vite(['resources/js/admin/form-handler.js'])
    @endif
    @stack('scripts')
</body>
</html>
