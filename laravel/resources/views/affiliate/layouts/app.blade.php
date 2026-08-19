<!DOCTYPE html>
<html lang="{{ current_lang() }}" @if(current_lang() === 'ar') dir="rtl" @endif x-data="{ sidebarOpen: true }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) — {{ __('Affiliate Panel') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite([is_rtl() ? 'resources/css/admin/app.rtl.css' : 'resources/css/admin/app.ltr.css', 'resources/js/admin/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col fixed md:static inset-y-0 start-0 z-30 transition-transform"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0 md:w-16'">
            <div class="h-16 flex items-center px-4 border-b border-slate-800">
                <span class="font-bold text-lg text-white" x-show="sidebarOpen">{{ __('Brown Box') }}</span>
                <i class="fa-solid fa-handshake text-amber-500 text-xl" x-show="!sidebarOpen"></i>
            </div>

            <nav class="flex-1 overflow-y-auto py-3 space-y-1">
                @php
                    $links = [
                        ['route' => 'affiliate.dashboard', 'pattern' => 'affiliate.dashboard', 'icon' => 'fa-gauge-high', 'label' => 'Dashboard'],
                        ['route' => 'affiliate.orders.index', 'pattern' => 'affiliate.orders.*', 'icon' => 'fa-cart-flatbed', 'label' => 'Orders'],
                        ['route' => 'affiliate.commissions.index', 'pattern' => 'affiliate.commissions.*', 'icon' => 'fa-sack-dollar', 'label' => 'Commissions'],
                        ['route' => 'affiliate.balance.index', 'pattern' => 'affiliate.balance.*', 'icon' => 'fa-wallet', 'label' => 'Balance'],
                        ['route' => 'affiliate.payouts.index', 'pattern' => 'affiliate.payouts.*', 'icon' => 'fa-money-check-dollar', 'label' => 'Payouts'],
                        ['route' => 'affiliate.profile.edit', 'pattern' => 'affiliate.profile.*', 'icon' => 'fa-user', 'label' => 'Profile'],
                    ];
                @endphp

                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}" class="admin-nav-link {{ request()->routeIs($link['pattern']) ? 'active' : '' }}">
                        <i class="fa-solid {{ $link['icon'] }} w-5"></i> <span x-show="sidebarOpen">{{ __($link['label']) }}</span>
                    </a>
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
                        @yield('breadcrumb', __('Affiliate Panel'))
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth('affiliate')->user()->name ?? 'Affiliate') }}"
                                class="w-9 h-9 rounded-full object-cover border border-slate-200" alt="{{ __('admin.avatar') }}">
                            <span class="text-sm font-medium text-slate-700">{{ auth('affiliate')->user()->name ?? '' }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>
                        <div x-show="open" x-cloak class="absolute end-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 text-sm">
                            <a href="{{ route('affiliate.profile.edit') }}" class="block px-4 py-2 hover:bg-slate-50"><i class="fa-solid fa-user me-2"></i>{{ __('Profile') }}</a>
                            <form method="POST" action="{{ route('affiliate.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-start px-4 py-2 hover:bg-slate-50 text-red-600">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>{{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Stats bar --}}
            @if ($affiliateStats)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 px-6 pt-4">
                    <div class="bg-white rounded-lg border border-slate-200 px-4 py-3">
                        <p class="text-xs text-slate-500">{{ __('Available Balance') }}</p>
                        <p class="text-lg font-bold text-emerald-600">{{ money_format($affiliateStats['available_balance']) }}</p>
                    </div>
                    <div class="bg-white rounded-lg border border-slate-200 px-4 py-3">
                        <p class="text-xs text-slate-500">{{ __('Total Earned') }}</p>
                        <p class="text-lg font-bold text-slate-800">{{ money_format($affiliateStats['total_earned']) }}</p>
                    </div>
                    <div class="bg-white rounded-lg border border-slate-200 px-4 py-3">
                        <p class="text-xs text-slate-500">{{ __('Pending Commissions') }}</p>
                        <p class="text-lg font-bold text-amber-600">{{ money_format($affiliateStats['pending_commissions']) }}</p>
                    </div>
                    <div class="bg-white rounded-lg border border-slate-200 px-4 py-3">
                        <p class="text-xs text-slate-500">{{ __('Total Orders') }}</p>
                        <p class="text-lg font-bold text-slate-800">{{ $affiliateStats['total_orders'] }}</p>
                    </div>
                </div>
            @endif

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
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
