@extends('admin.layouts.app')

@section('title', __('Flash Sales'))

@section('breadcrumb')
    <span>{{ __('Flash Sales') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Flash Sales') }}</h1>
        <a href="{{ route('admin.flash-sales.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Flash Sale') }}
        </a>
    </div>

    <x-admin.table
        id="flash-sales-table"
        :ajax-url="route('admin.flash-sales.data')"
        :columns="[
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'starts_at', 'name' => 'starts_at', 'title' => __('Starts At')],
            ['data' => 'ends_at', 'name' => 'ends_at', 'title' => __('Ends At')],
            ['data' => 'items_count', 'name' => 'items_count', 'title' => __('Items'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.flash-sales.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="status" :options="[
                        'active' => __('Active'),
                        'upcoming' => __('Upcoming'),
                        'ended' => __('Ended'),
                    ]" :selected="$filters['status'] ?? null" :placeholder="__('All')" />
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
