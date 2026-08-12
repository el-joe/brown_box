@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('breadcrumb')
    <span>{{ __('Dashboard') }}</span>
@endsection

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500">{{ __('admin.todays_orders') }}</div>
                <div class="text-lg font-semibold text-slate-800">{{ $todayOrders }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500">{{ __('admin.todays_revenue') }}</div>
                <div class="text-lg font-semibold text-slate-800">{{ money_format($todayRevenue) }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500">{{ __('admin.pending_payments') }}</div>
                <div class="text-lg font-semibold text-slate-800">{{ $pendingPayments }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-red-100 text-red-700 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500">{{ __('admin.low_stock_items') }}</div>
                <div class="text-lg font-semibold text-slate-800">{{ $lowStockCount }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-admin.card :title="__('admin.recent_orders')">
                <x-slot:actions>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">
                        {{ __('admin.view_all') }}
                    </a>
                </x-slot:actions>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                                <th class="py-2">{{ __('admin.order_number') }}</th>
                                <th class="py-2">{{ __('admin.customer') }}</th>
                                <th class="py-2">{{ __('admin.items') }}</th>
                                <th class="py-2 text-right">{{ __('admin.total') }}</th>
                                <th class="py-2">{{ __('status') }}</th>
                                <th class="py-2">{{ __('admin.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr class="border-b border-slate-100">
                                    <td class="py-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="py-2 text-slate-700">{{ $order->customer?->name ?? __('Guest') }}</td>
                                    <td class="py-2 text-slate-500">{{ $order->items_count }}</td>
                                    <td class="py-2 text-right font-medium">{{ money_format((float) $order->total_amount) }}</td>
                                    <td class="py-2"><x-admin.badge :status="$order->status" /></td>
                                    <td class="py-2 text-slate-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-400">{{ __('admin.no_recent_orders') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>

        <div>
            <x-admin.card :title="__('admin.monthly_revenue')">
                <canvas id="monthly-revenue-chart" height="220"></canvas>
            </x-admin.card>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const ctx = document.getElementById('monthly-revenue-chart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($monthlyRevenue->keys()),
                    datasets: [{
                        label: @json(__('admin.monthly_revenue')),
                        data: @json($monthlyRevenue->values()),
                        backgroundColor: '#2563eb',
                        borderRadius: 4,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: { beginAtZero: true },
                    },
                },
            });
        })();
    </script>
@endpush
