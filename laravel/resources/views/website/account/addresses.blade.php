@extends('website.layouts.app')

@section('title', __('website.my_addresses'))

@php
    $citiesByGovernorate = $governorates->mapWithKeys(fn ($gov) => [
        $gov->id => $gov->cities->map(fn ($city) => [
            'id' => $city->id,
            'name' => current_lang() === 'ar' ? $city->name_ar : $city->name_en,
        ]),
    ])->toArray();
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.my_addresses'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16" id="addresses-page"
        x-data="addressManager({
            storeUrl: @js(route('web.account.addresses.store', ['lang' => current_lang()])),
            governorates: @js($governorates->map(fn ($gov) => ['id' => $gov->id, 'name' => current_lang() === 'ar' ? $gov->name_ar : $gov->name_en])),
            citiesByGovernorate: @js($citiesByGovernorate),
        })">
        <div class="web-account-layout">
            @include('website.account._sidebar', ['active' => 'addresses'])

            <div class="min-w-0">
                <div class="web-account-card">
                    <div class="web-account-card-head">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">{{ __('website.my_addresses') }}</h1>
                            <p class="web-account-card-sub">{{ __('website.manage_addresses_desc') }}</p>
                        </div>
                        <button type="button" class="web-btn-primary" @click="openCreate()">
                            <i class="fa-solid fa-plus"></i> {{ __('website.add_new_address') }}
                        </button>
                    </div>

                    <div id="address-list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse ($addresses as $address)
                            <div class="web-address-card {{ $address->is_default ? 'is-default' : '' }}" data-address-id="{{ $address->id }}">
                                @if ($address->is_default)
                                    <span class="web-address-badge">{{ __('website.default') }}</span>
                                @endif
                                @if ($address->label)
                                    <p class="text-xs font-semibold text-brand uppercase mb-1">{{ $address->label }}</p>
                                @endif
                                <p class="font-bold text-sm text-slate-900">{{ $address->name }}</p>
                                <p class="text-sm text-slate-600 mt-0.5">{{ $address->phone }}</p>
                                <p class="text-sm text-slate-400 mt-1 leading-relaxed">
                                    {{ $address->address_line }}, {{ current_lang() === 'ar' ? $address->city?->name_ar : $address->city?->name_en }},
                                    {{ current_lang() === 'ar' ? $address->governorate?->name_ar : $address->governorate?->name_en }}
                                </p>
                                <div class="flex items-center gap-4 mt-3">
                                    <button type="button" class="text-slate-500 hover:text-brand text-sm font-semibold"
                                        @click="openEdit(@js([
                                            'id' => $address->id,
                                            'label' => $address->label,
                                            'name' => $address->name,
                                            'phone' => $address->phone,
                                            'governorate_id' => $address->governorate_id,
                                            'city_id' => $address->city_id,
                                            'address_line' => $address->address_line,
                                            'is_default' => (bool) $address->is_default,
                                        ]))">
                                        <i class="fa-solid fa-pen"></i> {{ __('website.edit') }}
                                    </button>
                                    <form action="{{ route('web.account.addresses.destroy', ['lang' => current_lang(), 'address' => $address->id]) }}" method="POST" class="address-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold"><i class="fa-solid fa-trash"></i> {{ __('website.delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 col-span-full py-6 text-center">{{ __('website.no_addresses_yet') }}</p>
                        @endforelse

                        <button type="button" class="web-add-account-address-card" @click="openCreate()">
                            <i class="fa-solid fa-plus text-xl"></i>
                            <span class="text-sm font-medium">{{ __('website.add_new_address') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create / Edit address modal --}}
        <div class="web-modal-overlay" :class="{ 'is-open': open }" @click.self="close()">
            <div class="web-modal-box" x-show="open" x-transition>
                <div class="web-modal-header">
                    <h3 class="font-bold text-lg" x-text="mode === 'edit' ? '{{ __('website.edit_address') }}' : '{{ __('website.add_new_address') }}'"></h3>
                    <button type="button" class="web-modal-close" @click="close()"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form @submit.prevent="submit()">
                    <div class="web-modal-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @include('website.account._address_form_fields')
                    </div>
                    <div class="web-modal-footer">
                        <button type="button" class="web-btn-outline" @click="close()">{{ __('website.cancel') }}</button>
                        <button type="submit" class="web-btn-primary" :disabled="saving">
                            <span x-show="!saving">{{ __('website.save_address_btn') }}</span>
                            <span x-show="saving"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div id="web-account-toast" class="web-toast">
        <i class="fa-solid fa-circle-check"></i>
        <span></span>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/account.js'])
@endpush
