@extends('admin.layouts.app')

@section('title', __('Orders'))

@section('breadcrumb')
    <span>{{ __('Orders') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Orders') }}</h1>
        <a href="{{ route('admin.orders.pos') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-cash-register me-1"></i>{{ __('POS') }}
        </a>
    </div>

    <x-admin.filter-card class="mb-6">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Order #') }}</label>
                <input type="text" name="order_number" value="{{ $filters['order_number'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Customer (name/email/phone)') }}</label>
                <input type="text" name="customer" value="{{ $filters['customer'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Order Status') }}</label>
                <x-admin.select name="status" :options="[
                    'pending' => __('Pending'), 'confirmed' => __('Confirmed'), 'processing' => __('Processing'),
                    'shipped' => __('Shipped'), 'delivered' => __('Delivered'), 'cancelled' => __('Cancelled'), 'refunded' => __('Refunded'),
                ]" :selected="$filters['status'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Payment Status') }}</label>
                <x-admin.select name="payment_status" :options="[
                    'unpaid' => __('Unpaid'), 'pending_verification' => __('Pending Verification'),
                    'paid' => __('Paid'), 'failed' => __('Failed'), 'refunded' => __('Refunded'),
                ]" :selected="$filters['payment_status'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Payment Gateway') }}</label>
                <x-admin.select name="payment_gateway" :options="[
                    'bank_transfer' => __('Bank Transfer'), 'vodafone_cash' => __('Vodafone Cash'),
                    'instapay' => __('Instapay'), 'paymob' => __('Paymob'),
                ]" :selected="$filters['payment_gateway'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Affiliate ID') }}</label>
                <input type="number" name="affiliate_id" value="{{ $filters['affiliate_id'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Has Refund') }}</label>
                <x-admin.select name="has_refund" :options="[1 => __('Yes'), 0 => __('No')]" :selected="$filters['has_refund'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table
        id="orders-table"
        :ajax-url="route('admin.orders.data', array_filter($filters))"
        :columns="[
            ['data' => 'order_number', 'name' => 'order_number', 'title' => __('Order #')],
            ['data' => 'customer', 'name' => 'customer.name', 'title' => __('Customer'), 'orderable' => false],
            ['data' => 'date', 'name' => 'created_at', 'title' => __('Date')],
            ['data' => 'items', 'name' => 'items_count', 'title' => __('Items'), 'orderable' => false, 'searchable' => false],
            ['data' => 'total', 'name' => 'total_amount', 'title' => __('Total')],
            ['data' => 'payment_gateway', 'name' => 'payment_gateway', 'title' => __('Gateway'), 'orderable' => false, 'searchable' => false],
            ['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('Payment'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Order Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'shipping_status', 'name' => 'shipping_status', 'title' => __('Shipping'), 'orderable' => false, 'searchable' => false],
            ['data' => 'commission', 'name' => 'commission', 'title' => __('Commission'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    />
@endsection
