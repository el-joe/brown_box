@extends('admin.layouts.app')

@section('title', __('Stock Movements'))

@section('breadcrumb')
    <a href="{{ route('admin.stock.index') }}" class="hover:text-slate-700">{{ __('Stock') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Movements') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Stock Movements') }}</h1>
    </div>

    <x-admin.table
        id="stock-movements-table"
        :ajax-url="route('admin.stock.movements.data')"
        :columns="[
            ['data' => 'date', 'name' => 'created_at', 'title' => __('Date')],
            ['data' => 'product', 'name' => 'product', 'title' => __('Product')],
            ['data' => 'variant', 'name' => 'variant', 'title' => __('Variant'), 'orderable' => false, 'searchable' => false],
            ['data' => 'warehouse', 'name' => 'warehouse', 'title' => __('Warehouse'), 'orderable' => false, 'searchable' => false],
            ['data' => 'type', 'name' => 'type', 'title' => __('Type')],
            ['data' => 'qty_change', 'name' => 'qty_change', 'title' => __('Qty Change'), 'orderable' => false, 'searchable' => false],
            ['data' => 'before', 'name' => 'before_qty', 'title' => __('Before'), 'orderable' => false, 'searchable' => false],
            ['data' => 'after', 'name' => 'after_qty', 'title' => __('After'), 'orderable' => false, 'searchable' => false],
            ['data' => 'reference', 'name' => 'reference', 'title' => __('Reference'), 'orderable' => false, 'searchable' => false],
            ['data' => 'note', 'name' => 'note', 'title' => __('Note'), 'orderable' => false, 'searchable' => false],
            ['data' => 'admin', 'name' => 'admin', 'title' => __('Admin'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.stock.movements') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Product') }}</label>
                    <input type="text" name="product" value="{{ $filters['product'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Warehouse') }}</label>
                    <x-admin.select name="warehouse_id" :options="$warehouses" :selected="$filters['warehouse_id'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Type') }}</label>
                    <x-admin.select name="type" :options="['purchase' => __('Purchase'), 'sale' => __('Sale'), 'adjustment' => __('Adjustment'), 'return' => __('Return'), 'transfer' => __('Transfer'), 'in' => __('In'), 'out' => __('Out')]" :selected="$filters['type'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('From') }}</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('To') }}</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="flex items-end md:col-span-5">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </x-slot:filters>
    </x-admin.table>
@endsection
