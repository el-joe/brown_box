@extends('admin.layouts.app')

@section('title', __('Purchases'))

@section('breadcrumb')
    <span>{{ __('Purchases') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Purchases') }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.purchases.export', request()->only(['invoice_number', 'supplier_id', 'warehouse_id', 'status', 'date_from', 'date_to'])) }}"
                class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-file-excel me-1"></i>{{ __('Export') }}
            </a>
            <a href="{{ route('admin.purchases.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                <i class="fa-solid fa-plus me-1"></i>{{ __('New Purchase') }}
            </a>
        </div>
    </div>

    <x-admin.table
        id="purchases-table"
        :ajax-url="route('admin.purchases.data')"
        :columns="[
            ['data' => 'invoice_number', 'name' => 'invoice_number', 'title' => __('Invoice #')],
            ['data' => 'supplier', 'name' => 'supplier.name', 'title' => __('Supplier'), 'orderable' => false],
            ['data' => 'warehouse', 'name' => 'warehouse.name', 'title' => __('Warehouse'), 'orderable' => false],
            ['data' => 'date', 'name' => 'created_at', 'title' => __('Date')],
            ['data' => 'items_count', 'name' => 'items_count', 'title' => __('Items'), 'orderable' => false, 'searchable' => false],
            ['data' => 'net_amount', 'name' => 'net_amount', 'title' => __('Total'), 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'searchable' => false],
            ['data' => 'admin', 'name' => 'admin.name', 'title' => __('Admin'), 'orderable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.purchases.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Invoice #') }}</label>
                    <input type="text" name="invoice_number" value="{{ $filters['invoice_number'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Supplier') }}</label>
                    <x-admin.select name="supplier_id" :options="$suppliers" :selected="$filters['supplier_id'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Warehouse') }}</label>
                    <x-admin.select name="warehouse_id" :options="$warehouses" :selected="$filters['warehouse_id'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="status" :options="['draft' => __('Draft'), 'confirmed' => __('Confirmed')]" :selected="$filters['status'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('From') }}</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('To') }}</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="flex items-end md:col-span-6">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </x-slot:filters>
    </x-admin.table>

    <x-admin.confirm-delete />
@endsection
