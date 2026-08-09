@extends('admin.layouts.app')

@section('title', __('Customers'))

@section('breadcrumb')
    <span>{{ __('Customers') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Customers') }}</h1>
    </div>

    <x-admin.filter-card class="mb-6">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Email') }}</label>
                <input type="text" name="email" value="{{ $filters['email'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Phone') }}</label>
                <input type="text" name="phone" value="{{ $filters['phone'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                <x-admin.select name="status" :options="[1 => __('Active'), 0 => __('Inactive')]" :selected="$filters['status'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table
        id="customers-table"
        :ajax-url="route('admin.customers.data', array_filter($filters))"
        :columns="[
            ['data' => 'avatar', 'name' => 'avatar', 'title' => __('Avatar'), 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'email', 'name' => 'email', 'title' => __('Email')],
            ['data' => 'phone', 'name' => 'phone', 'title' => __('Phone')],
            ['data' => 'orders_count', 'name' => 'orders_count', 'title' => __('Orders'), 'searchable' => false],
            ['data' => 'total_spent', 'name' => 'total_spent', 'title' => __('Total Spent'), 'searchable' => false],
            ['data' => 'registered_at', 'name' => 'created_at', 'title' => __('Registered At')],
            ['data' => 'status', 'name' => 'is_active', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    />
@endsection
