@extends('admin.layouts.app')

@section('title', __('Affiliates'))

@section('breadcrumb')
    <span>{{ __('Affiliates') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Affiliates') }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.affiliates.payouts.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-money-check-dollar me-1"></i>{{ __('Payout Requests') }}
            </a>
            <a href="{{ route('admin.affiliates.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                <i class="fa-solid fa-plus me-1"></i>{{ __('Add Affiliate') }}
            </a>
        </div>
    </div>

    <x-admin.table
        id="affiliates-table"
        :ajax-url="route('admin.affiliates.data')"
        :columns="[
            ['data' => 'name', 'name' => 'name', 'title' => __('Name'), 'orderable' => false],
            ['data' => 'email', 'name' => 'email', 'title' => __('Email'), 'orderable' => false],
            ['data' => 'code', 'name' => 'code', 'title' => __('Code')],
            ['data' => 'commission_type', 'name' => 'commission_type', 'title' => __('Commission Type'), 'orderable' => false, 'searchable' => false],
            ['data' => 'balance', 'name' => 'balance', 'title' => __('Balance'), 'orderable' => false, 'searchable' => false],
            ['data' => 'total_earned', 'name' => 'total_earned', 'title' => __('Total Earned'), 'orderable' => false, 'searchable' => false],
            ['data' => 'orders_count', 'name' => 'orders_count', 'title' => __('Orders'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.affiliates.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Email') }}</label>
                    <input type="text" name="email" value="{{ $filters['email'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Code') }}</label>
                    <input type="text" name="code" value="{{ $filters['code'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Commission Type') }}</label>
                    <x-admin.select name="commission_type" :options="[
                        'fixed_all_orders' => __('Fixed for All Orders'),
                        'per_category' => __('Per Category'),
                    ]" :selected="$filters['commission_type'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="is_active" :options="[1 => __('Active'), 0 => __('Inactive')]" :selected="$filters['is_active'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </x-slot:filters>
    </x-admin.table>
@endsection
