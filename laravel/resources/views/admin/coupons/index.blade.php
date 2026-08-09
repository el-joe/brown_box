@extends('admin.layouts.app')

@section('title', __('Coupons'))

@section('breadcrumb')
    <span>{{ __('Coupons') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Coupons') }}</h1>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Coupon') }}
        </a>
    </div>

    <x-admin.table
        id="coupons-table"
        :ajax-url="route('admin.coupons.data')"
        :columns="[
            ['data' => 'code', 'name' => 'code', 'title' => __('Code')],
            ['data' => 'type', 'name' => 'type', 'title' => __('Type'), 'orderable' => false, 'searchable' => false],
            ['data' => 'value', 'name' => 'value', 'title' => __('Value'), 'orderable' => false, 'searchable' => false],
            ['data' => 'min_order', 'name' => 'min_order', 'title' => __('Min Order'), 'orderable' => false, 'searchable' => false],
            ['data' => 'usage', 'name' => 'usage', 'title' => __('Used / Max'), 'orderable' => false, 'searchable' => false],
            ['data' => 'validity', 'name' => 'validity', 'title' => __('Validity'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.coupons.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Code') }}</label>
                    <input type="text" name="code" value="{{ $filters['code'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Type') }}</label>
                    <x-admin.select name="type" :options="[
                        'free_shipping' => __('Free Shipping'),
                        'percentage' => __('Percentage'),
                        'fixed' => __('Fixed Amount'),
                    ]" :selected="$filters['type'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="is_active" :options="[1 => __('Active'), 0 => __('Inactive')]" :selected="$filters['is_active'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field flex items-end pb-2">
                    <x-admin.checkbox name="valid_now" value="1" :checked="($filters['valid_now'] ?? false) == '1'" :label="__('Valid now')" />
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
