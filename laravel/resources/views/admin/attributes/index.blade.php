@extends('admin.layouts.app')

@section('title', __('Attributes'))

@section('breadcrumb')
    <span>{{ __('Attributes') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Attributes') }}</h1>
        <a href="{{ route('admin.attributes.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Attribute') }}
        </a>
    </div>

    <x-admin.table
        id="attributes-table"
        :ajax-url="route('admin.attributes.data')"
        :columns="[
            ['data' => 'name_en', 'name' => 'name_en', 'title' => __('Name (EN)')],
            ['data' => 'name_ar', 'name' => 'name_ar', 'title' => __('Name (AR)')],
            ['data' => 'values_count', 'name' => 'values_count', 'title' => __('Values'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.attributes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
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
@endsection
