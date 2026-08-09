@extends('admin.layouts.app')

@section('title', __('Warehouses'))

@section('breadcrumb')
    <span>{{ __('Warehouses') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Warehouses') }}</h1>
        <a href="{{ route('admin.warehouses.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Warehouse') }}
        </a>
    </div>

    <x-admin.table
        id="warehouses-table"
        :ajax-url="route('admin.warehouses.data')"
        :columns="[
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'governorate', 'name' => 'governorate', 'title' => __('Governorate'), 'orderable' => false, 'searchable' => false],
            ['data' => 'city', 'name' => 'city', 'title' => __('City'), 'orderable' => false, 'searchable' => false],
            ['data' => 'stocks_count', 'name' => 'stocks_count', 'title' => __('Stock Items'), 'orderable' => false, 'searchable' => false],
            ['data' => 'is_default', 'name' => 'is_default', 'title' => __('Default'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.warehouses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
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

    <x-admin.confirm-delete />
@endsection
