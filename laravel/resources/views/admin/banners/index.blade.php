@extends('admin.layouts.app')

@section('title', __('Banners'))

@section('breadcrumb')
    <span>{{ __('Banners') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Banners') }}</h1>
        <a href="{{ route('admin.banners.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Banner') }}
        </a>
    </div>

    <x-admin.table
        id="banners-table"
        :ajax-url="route('admin.banners.data')"
        :columns="[
            ['data' => 'image', 'name' => 'image', 'title' => __('Image'), 'orderable' => false, 'searchable' => false],
            ['data' => 'title_en', 'name' => 'title_en', 'title' => __('Title (EN)')],
            ['data' => 'title_ar', 'name' => 'title_ar', 'title' => __('Title (AR)')],
            ['data' => 'type', 'name' => 'type', 'title' => __('Type')],
            ['data' => 'sort_order', 'name' => 'sort_order', 'title' => __('Order')],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.banners.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" value="{{ $filters['title'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Type') }}</label>
                    <x-admin.select name="type" :options="['product' => __('Product'), 'category' => __('Category'), 'external' => __('External')]" :selected="$filters['type'] ?? null" :placeholder="__('All')" />
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
