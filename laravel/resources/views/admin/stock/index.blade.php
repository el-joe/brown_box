@extends('admin.layouts.app')

@section('title', __('Stock'))

@section('breadcrumb')
    <span>{{ __('Stock') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Stock') }}</h1>
        <a href="{{ route('admin.stock.movements') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-clock-rotate-left me-1"></i>{{ __('Movements Log') }}
        </a>
    </div>

    <x-admin.table
        id="stock-table"
        :ajax-url="route('admin.stock.data')"
        :columns="[
            ['data' => 'image', 'name' => 'image', 'title' => __('Image'), 'orderable' => false, 'searchable' => false],
            ['data' => 'product', 'name' => 'product', 'title' => __('Product')],
            ['data' => 'variant', 'name' => 'variant', 'title' => __('Variant'), 'orderable' => false, 'searchable' => false],
            ['data' => 'warehouse', 'name' => 'warehouse', 'title' => __('Warehouse'), 'orderable' => false, 'searchable' => false],
            ['data' => 'qty', 'name' => 'qty', 'title' => __('Current Qty')],
            ['data' => 'min_qty_alert', 'name' => 'min_qty_alert', 'title' => __('Min Alert')],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.stock.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Product') }}</label>
                    <input type="text" name="product" value="{{ $filters['product'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Warehouse') }}</label>
                    <x-admin.select name="warehouse_id" :options="$warehouses" :selected="$filters['warehouse_id'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="status" :options="['low' => __('Low Stock'), 'out' => __('Out of Stock')]" :selected="$filters['status'] ?? null" :placeholder="__('All')" />
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
