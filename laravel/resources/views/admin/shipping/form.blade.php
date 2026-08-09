@extends('admin.layouts.app')

@php
    $isEdit = $company->exists;
    $validateUrl = $isEdit ? route('admin.shipping.update.validate', $company) : route('admin.shipping.validate');
    $submitUrl = $isEdit ? route('admin.shipping.update', $company) : route('admin.shipping.store');
@endphp

@section('title', $isEdit ? __('Edit Shipping Company') : __('Create Shipping Company'))

@section('breadcrumb')
    <a href="{{ route('admin.shipping.index') }}" class="hover:text-slate-700">{{ __('Shipping Companies') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form id="shipping-company-form" method="POST" enctype="multipart/form-data"
    x-data="shippingCompanyForm({
        rates: {{ Js::from($rates) }},
        citiesUrl: {{ Js::from(url('admin/shipping/cities-by-governorate')) }},
    })"
    x-init="init()"
    @submit.prevent="AdminForm.submit('shipping-company-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.shipping.index')))">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('General')">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Shipping Rates')">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-2 py-2 text-start w-1/4">{{ __('Governorate') }}</th>
                                <th class="px-2 py-2 text-start w-1/4">{{ __('City (optional)') }}</th>
                                <th class="px-2 py-2 text-start w-28">{{ __('Price (EGP)') }}</th>
                                <th class="px-2 py-2 text-start w-28">{{ __('Estimated Days') }}</th>
                                <th class="px-2 py-2"></th>
                                <th class="px-2 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in rates" :key="row.key">
                                <tr class="border-t border-slate-100 align-top">
                                    <td class="px-2 py-2">
                                        <select :name="'rates['+index+'][governorate_id]'" x-model="row.governorate_id" @change="loadCities(index)" class="w-full rounded-lg border-slate-300 text-xs">
                                            <option value=""></option>
                                            @foreach ($governorates as $id => $label)
                                                <option value="{{ $id }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-2">
                                        <select :name="'rates['+index+'][city_id]'" x-model="row.city_id" class="w-full rounded-lg border-slate-300 text-xs">
                                            <option value="">{{ __('Whole governorate') }}</option>
                                            <template x-for="(label, id) in row.cities" :key="id">
                                                <option :value="id" x-text="label"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" min="0" step="0.01" :name="'rates['+index+'][price]'" x-model.number="row.price" class="w-24 rounded-lg border-slate-300 text-xs">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" min="0" step="1" :name="'rates['+index+'][estimated_days]'" x-model.number="row.estimated_days" class="w-24 rounded-lg border-slate-300 text-xs">
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" @click="applyToAllCities(index)" x-show="row.governorate_id" x-cloak class="text-[11px] text-amber-600 hover:text-amber-700 whitespace-nowrap">
                                            {{ __('Apply to all cities') }}
                                        </button>
                                    </td>
                                    <td class="px-2 py-2 text-end">
                                        <button type="button" @click="removeRate(index)" class="text-slate-400 hover:text-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!rates.length">
                                <td colspan="6" class="px-2 py-6 text-center text-slate-400">{{ __('No rates yet — add a governorate or city rate.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" @click="addRate()" class="mt-4 px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">
                    <i class="fa-solid fa-plus me-1"></i>{{ __('Add Rate') }}
                </button>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Logo')">
                <x-admin.image-upload name="logo" :current="$company->logo" />
            </x-admin.card>

            <x-admin.card :title="__('Status')">
                <x-admin.checkbox name="is_active" :checked="old('is_active', $company->is_active ?? true)" :label="__('Active')" />
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Shipping Company') : __('Create Shipping Company') }}
                </button>
                <a href="{{ route('admin.shipping.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
    <script>
        function shippingCompanyForm({ rates, citiesUrl }) {
            return {
                rates: rates.map((rate) => ({
                    key: Date.now() + Math.random(),
                    governorate_id: rate.governorate_id ?? '',
                    city_id: rate.city_id ?? '',
                    price: rate.price ?? 0,
                    estimated_days: rate.estimated_days ?? '',
                    cities: {},
                })),
                citiesUrl,
                citiesCache: {},

                init() {
                    this.rates.forEach((row, index) => {
                        if (row.governorate_id) {
                            this.loadCities(index, true);
                        }
                    });
                },

                addRate() {
                    this.rates.push({
                        key: Date.now() + Math.random(),
                        governorate_id: '',
                        city_id: '',
                        price: 0,
                        estimated_days: '',
                        cities: {},
                    });
                },

                removeRate(index) {
                    this.rates.splice(index, 1);
                },

                async fetchCities(governorateId) {
                    if (this.citiesCache[governorateId]) {
                        return this.citiesCache[governorateId];
                    }

                    const res = await fetch(`${this.citiesUrl}/${governorateId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } });
                    const cities = await res.json();
                    this.citiesCache[governorateId] = cities;

                    return cities;
                },

                async loadCities(index, keepCity = false) {
                    const row = this.rates[index];

                    if (! keepCity) {
                        row.city_id = '';
                    }

                    row.cities = row.governorate_id ? await this.fetchCities(row.governorate_id) : {};
                },

                async applyToAllCities(index) {
                    const row = this.rates[index];

                    if (! row.governorate_id) {
                        return;
                    }

                    const cities = await this.fetchCities(row.governorate_id);

                    Object.keys(cities).forEach((cityId) => {
                        this.rates.push({
                            key: Date.now() + Math.random(),
                            governorate_id: row.governorate_id,
                            city_id: cityId,
                            price: row.price,
                            estimated_days: row.estimated_days,
                            cities,
                        });
                    });
                },
            };
        }
    </script>
@endpush
