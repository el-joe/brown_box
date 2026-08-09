@extends('affiliate.layouts.app')

@section('title', __('Dashboard'))
@section('breadcrumb', __('Dashboard'))

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">{{ __('Available Balance') }}</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ money_format($affiliateStats['available_balance']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">{{ __('Total Earned') }}</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ money_format($affiliateStats['total_earned']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">{{ __('Pending Commissions') }}</p>
            <p class="text-3xl font-bold text-amber-600 mt-1">{{ money_format($affiliateStats['pending_commissions']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">{{ __('Total Orders') }}</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $affiliateStats['total_orders'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-slate-800">{{ __('Your Referral Link') }}</h2>
            </div>
            <div class="flex items-center gap-2" x-data="{ copied: false }">
                <input type="text" readonly value="{{ $referralLink }}" id="referral-link"
                    class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm bg-slate-50 text-slate-600">
                <button type="button"
                    @click="navigator.clipboard.writeText(document.getElementById('referral-link').value); copied = true; setTimeout(() => copied = false, 2000)"
                    class="shrink-0 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span>
                </button>
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ __('Share this link — orders placed through it are attributed to you.') }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-2"><i class="fa-solid fa-circle-info text-amber-500 me-1"></i> {{ __('How Your Commission Works') }}</h2>
            @if ($affiliate->commission_type === 'fixed_all_orders')
                <p class="text-sm text-slate-600">
                    {{ __('You earn a flat :rate% commission on the subtotal of every order placed through your referral link.', ['rate' => $affiliate->fixed_commission_rate]) }}
                </p>
            @else
                <p class="text-sm text-slate-600">
                    {{ __('Your commission rate depends on the category of each item ordered, and may be tiered by order value.') }}
                </p>
            @endif
            <p class="text-xs text-slate-500 mt-2">{{ __('Commissions become available for payout 14 days after the order is delivered.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-3">{{ __('Recent Commissions') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-start text-slate-500 border-b border-slate-200">
                            <th class="py-2 pe-2 text-start">{{ __('Order') }}</th>
                            <th class="py-2 pe-2 text-start">{{ __('Amount') }}</th>
                            <th class="py-2 text-start">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentCommissions as $commission)
                            <tr class="border-b border-slate-100">
                                <td class="py-2 pe-2">{{ $commission->order?->order_number ?? '—' }}</td>
                                <td class="py-2 pe-2">{{ money_format($commission->amount) }}</td>
                                <td class="py-2">
                                    <x-affiliate.status-badge :status="$commission->display_status" />
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">{{ __('No commissions yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-3">{{ __('Recent Orders') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-start text-slate-500 border-b border-slate-200">
                            <th class="py-2 pe-2 text-start">{{ __('Order') }}</th>
                            <th class="py-2 pe-2 text-start">{{ __('Date') }}</th>
                            <th class="py-2 text-start">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr class="border-b border-slate-100">
                                <td class="py-2 pe-2">{{ $order->order_number }}</td>
                                <td class="py-2 pe-2">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="py-2">{{ money_format($order->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-slate-400">{{ __('No orders yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
