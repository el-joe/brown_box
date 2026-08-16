@extends('admin.layouts.app')

@section('title', __('Suppliers'))

@section('breadcrumb')
    <span>{{ __('Suppliers') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Suppliers') }}</h1>
        <a href="{{ route('admin.suppliers.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Supplier') }}
        </a>
    </div>

    <x-admin.table
        id="suppliers-table"
        :ajax-url="route('admin.suppliers.data')"
        :columns="[
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'phone', 'name' => 'phone', 'title' => __('Phone'), 'orderable' => false, 'searchable' => false],
            ['data' => 'email', 'name' => 'email', 'title' => __('Email'), 'orderable' => false, 'searchable' => false],
            ['data' => 'balance', 'name' => 'balance', 'title' => __('Balance'), 'orderable' => false, 'searchable' => false],
            ['data' => 'purchases_count', 'name' => 'purchases_count', 'title' => __('Purchases'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.suppliers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
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
