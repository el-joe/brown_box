@extends('admin.layouts.app')

@section('title', __('Reports'))

@section('breadcrumb')
    <span>{{ __('Reports') }}</span>
@endsection

@php
    $reports = [
        ['route' => 'admin.reports.sales', 'icon' => 'fa-chart-line', 'title' => __('Sales Report'), 'desc' => __('Orders, revenue, average order value over time.')],
        ['route' => 'admin.reports.purchases', 'icon' => 'fa-cart-shopping', 'title' => __('Purchases Report'), 'desc' => __('Purchases grouped by supplier and period.')],
        ['route' => 'admin.reports.expenses', 'icon' => 'fa-money-bill-wave', 'title' => __('Expenses Report'), 'desc' => __('Expenses grouped by category and period.')],
        ['route' => 'admin.reports.profit-loss', 'icon' => 'fa-scale-balanced', 'title' => __('Profit & Loss'), 'desc' => __('Revenue minus COGS and expenses.')],
        ['route' => 'admin.reports.affiliates', 'icon' => 'fa-handshake', 'title' => __('Affiliate Report'), 'desc' => __('Commissions earned, paid out, pending.')],
        ['route' => 'admin.reports.products', 'icon' => 'fa-box-open', 'title' => __('Product Performance'), 'desc' => __('Top selling products and revenue per product.')],
        ['route' => 'admin.reports.stock', 'icon' => 'fa-warehouse', 'title' => __('Stock Report'), 'desc' => __('Current stock levels and valuation.')],
        ['route' => 'admin.reports.customers', 'icon' => 'fa-users', 'title' => __('Customer Report'), 'desc' => __('New customers, repeat customers, LTV.')],
    ];
@endphp

@section('content')
    <h1 class="text-lg font-semibold text-slate-800 mb-6">{{ __('Financial Reports') }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($reports as $report)
            <a href="{{ route($report['route']) }}" class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:border-amber-400 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                    <i class="fa-solid {{ $report['icon'] }}"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">{{ $report['title'] }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $report['desc'] }}</p>
            </a>
        @endforeach
    </div>
@endsection
