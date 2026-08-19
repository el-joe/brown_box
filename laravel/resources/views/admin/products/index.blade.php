@extends('admin.layouts.app')

@section('title', __('Products'))

@section('breadcrumb')
    <span>{{ __('Products') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Products') }}</h1>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Product') }}
        </a>
    </div>

    <x-admin.filter-card class="mb-6">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field md:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name / SKU') }}</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Category') }}</label>
                <x-admin.select name="category_id" :options="$categories" :selected="$filters['category_id'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Brand') }}</label>
                <x-admin.select name="brand_id" :options="$brands" :selected="$filters['brand_id'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Min Price') }}</label>
                <input type="number" step="0.01" name="min_price" value="{{ $filters['min_price'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Max Price') }}</label>
                <input type="number" step="0.01" name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Stock') }}</label>
                <x-admin.select name="stock_status" :options="['in' => __('In Stock'), 'out' => __('Out of Stock')]" :selected="$filters['stock_status'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Has Variants') }}</label>
                <x-admin.select name="has_variants" :options="[1 => __('Yes'), 0 => __('No')]" :selected="$filters['has_variants'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                <x-admin.select name="is_active" :options="[1 => __('Active'), 0 => __('Inactive')]" :selected="$filters['is_active'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Featured') }}</label>
                <x-admin.select name="is_featured" :options="[1 => __('Yes'), 0 => __('No')]" :selected="$filters['is_featured'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Hero') }}</label>
                <x-admin.select name="is_hero" :options="[1 => __('Yes'), 0 => __('No')]" :selected="$filters['is_hero'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table
        id="products-table"
        :ajax-url="route('admin.products.data', array_filter($filters))"
        :columns="[
            ['data' => 'image', 'name' => 'image', 'title' => __('Image'), 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'sku', 'name' => 'sku', 'title' => __('SKU')],
            ['data' => 'category', 'name' => 'category.name', 'title' => __('Category'), 'orderable' => false],
            ['data' => 'brand', 'name' => 'brand.name', 'title' => __('Brand'), 'orderable' => false],
            ['data' => 'price', 'name' => 'price', 'title' => __('Price')],
            ['data' => 'stock', 'name' => 'stock', 'title' => __('Stock'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'featured', 'name' => 'featured', 'title' => __('Featured'), 'orderable' => false, 'searchable' => false],
            ['data' => 'hero', 'name' => 'hero', 'title' => __('Hero'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    />
@endsection
