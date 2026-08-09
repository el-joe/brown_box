@extends('admin.layouts.app')

@php
    $isEdit = $warehouse->exists;
    $validateUrl = $isEdit ? route('admin.warehouses.update.validate', $warehouse) : route('admin.warehouses.validate');
    $submitUrl = $isEdit ? route('admin.warehouses.update', $warehouse) : route('admin.warehouses.store');
@endphp

@section('title', $isEdit ? __('Edit Warehouse') : __('Create Warehouse'))

@section('breadcrumb')
    <a href="{{ route('admin.warehouses.index') }}" class="hover:text-slate-700">{{ __('Warehouses') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form id="warehouse-form" method="POST"
    x-data="{
        governorateId: {{ $warehouse->city?->governorate_id ?? 'null' }},
        cityId: {{ $warehouse->city_id ?? 'null' }},
        cities: {{ Js::from($cities) }},
        async loadCities() {
            this.cityId = null;
            if (! this.governorateId) { this.cities = {}; return; }
            const res = await fetch(`{{ url('admin/warehouses/cities-by-governorate') }}/${this.governorateId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } });
            this.cities = await res.json();
        },
    }"
    @submit.prevent="AdminForm.submit('warehouse-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.warehouses.index')))">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('General')">
                <div class="space-y-4">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $warehouse->name) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>

                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Address') }}</label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('address', $warehouse->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Governorate') }}</label>
                            <select name="governorate_id" x-model="governorateId" @change="loadCities()" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value=""></option>
                                @foreach ($governorates as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('City') }}</label>
                            <select name="city_id" x-model="cityId" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value=""></option>
                                <template x-for="(label, id) in cities" :key="id">
                                    <option :value="id" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Status')">
                <div class="space-y-3">
                    <x-admin.checkbox name="is_active" :checked="old('is_active', $warehouse->is_active ?? true)" :label="__('Active')" />
                    <x-admin.checkbox name="is_default" :checked="old('is_default', $warehouse->is_default ?? false)" :label="__('Default warehouse')" />
                </div>
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Warehouse') : __('Create Warehouse') }}
                </button>
                <a href="{{ route('admin.warehouses.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
