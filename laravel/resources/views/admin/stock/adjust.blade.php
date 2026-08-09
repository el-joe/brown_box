@extends('admin.layouts.app')

@section('title', __('Adjust Stock'))

@section('breadcrumb')
    <a href="{{ route('admin.stock.index') }}" class="hover:text-slate-700">{{ __('Stock') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Adjust') }}</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.stock.adjust', $stock) }}" class="max-w-2xl">
    @csrf

    <x-admin.card :title="__('Adjust Stock')">
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Product') }}</label>
                    <input type="text" value="{{ $stock->product?->getTranslation('name', app()->getLocale()) }}" readonly class="w-full rounded-lg border-slate-300 bg-slate-50 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Variant') }}</label>
                    <input type="text" value="{{ $stock->variant?->sku ?? __('N/A') }}" readonly class="w-full rounded-lg border-slate-300 bg-slate-50 text-sm">
                </div>
            </div>

            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Warehouse') }}</label>
                <x-admin.select name="warehouse_id" :options="$warehouses" :selected="old('warehouse_id', $stock->warehouse_id)" />
                @error('warehouse_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Adjustment Type') }}</label>
                    <x-admin.select name="direction" :options="['add' => __('Add'), 'deduct' => __('Deduct')]" :selected="old('direction', 'add')" />
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Quantity') }}</label>
                    <input type="number" name="qty" min="1" value="{{ old('qty', 1) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    @error('qty')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Reason / Note') }}</label>
                <textarea name="note" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('note') }}</textarea>
            </div>

            <div class="text-sm text-slate-500">
                {{ __('Current quantity') }}: <span class="font-semibold text-slate-700">{{ $stock->qty }}</span>
            </div>
        </div>
    </x-admin.card>

    <div class="flex items-center gap-2 mt-6">
        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            {{ __('Save Adjustment') }}
        </button>
        <a href="{{ route('admin.stock.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
            {{ __('Cancel') }}
        </a>
    </div>
</form>
@endsection
