@extends('website.layouts.app')

@section('title', __('website.my_addresses'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.my_addresses'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16" id="addresses-page" data-store-url="{{ route('web.account.addresses.store', ['lang' => current_lang()]) }}">
        <div class="web-account-layout">
            @include('website.account._sidebar', ['active' => 'addresses'])

            <div class="min-w-0">
                <div class="web-account-card">
                    <div class="web-account-card-head">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">{{ __('website.my_addresses') }}</h1>
                            <p class="web-account-card-sub">{{ __('website.manage_addresses_desc') }}</p>
                        </div>
                        <button type="button" id="add-address-btn" class="web-btn-primary">
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
                                <form action="{{ route('web.account.addresses.destroy', ['lang' => current_lang(), 'address' => $address->id]) }}" method="POST" class="address-delete-form mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold"><i class="fa-solid fa-trash"></i> {{ __('website.delete') }}</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-slate-400 col-span-full py-6 text-center">{{ __('website.no_addresses_yet') }}</p>
                        @endforelse

                        <button type="button" id="show-address-form-btn" class="web-add-account-address-card">
                            <i class="fa-solid fa-plus text-xl"></i>
                            <span class="text-sm font-medium">{{ __('website.add_new_address') }}</span>
                        </button>
                    </div>

                    {{-- Inline new-address form --}}
                    <form id="new-address-form" class="hidden mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-6"
                        method="POST" action="{{ route('web.account.addresses.store', ['lang' => current_lang()]) }}">
                        @csrf

                        <div class="web-checkout-field sm:col-span-2">
                            <label for="af-label">{{ __('website.address_label') }}</label>
                            <input id="af-label" name="label" type="text" placeholder="{{ __('website.address_label_placeholder') }}">
                        </div>
                        <div class="web-checkout-field">
                            <label for="af-name">{{ __('website.full_name') }}</label>
                            <input id="af-name" name="name" type="text" required>
                        </div>
                        <div class="web-checkout-field">
                            <label for="af-phone">{{ __('website.phone_number') }}</label>
                            <input id="af-phone" name="phone" type="tel" required>
                        </div>
                        <div class="web-checkout-field">
                            <label for="af-governorate">{{ __('website.governorate') }}</label>
                            <select id="af-governorate" name="governorate_id" required>
                                <option value="">—</option>
                                @foreach ($governorates as $gov)
                                    <option value="{{ $gov->id }}">{{ current_lang() === 'ar' ? $gov->name_ar : $gov->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="web-checkout-field">
                            <label for="af-city">{{ __('website.city') }}</label>
                            <select id="af-city" name="city_id" required>
                                <option value="">—</option>
                            </select>
                        </div>
                        <div class="web-checkout-field sm:col-span-2">
                            <label for="af-address">{{ __('website.street_address') }}</label>
                            <input id="af-address" name="address_line" type="text" required>
                        </div>
                        <label class="web-checkout-checkbox sm:col-span-2">
                            <input type="checkbox" name="is_default" value="1">
                            <span>{{ __('website.set_default') }}</span>
                        </label>

                        <div class="sm:col-span-2 flex justify-end gap-3">
                            <button type="button" id="cancel-address-form-btn" class="web-btn-outline px-6">{{ __('website.cancel') }}</button>
                            <button type="submit" id="save-address-btn" class="web-btn-primary px-8">{{ __('website.save_address_btn') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div id="web-account-toast" class="web-toast">
        <i class="fa-solid fa-circle-check"></i>
        <span></span>
    </div>
@endsection

@php
$addresses = $governorates->mapWithKeys(fn ($gov) => [
                $gov->id => $gov->cities->map(fn ($city) => [
                    'id' => $city->id,
                    'name' => current_lang() === 'ar' ? $city->name_ar : $city->name_en,
                ]),
            ])->toArray();
@endphp

@push('scripts')
    <script>
        window.accountData = {
            citiesByGovernorate: @json($addresses),
        };
    </script>
    @vite(['resources/js/website/account.js'])
@endpush
