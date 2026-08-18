@extends('admin.layouts.app')

@section('title', __('Cities'))

@section('breadcrumb')
    <a href="{{ route('admin.governorates.index') }}" class="hover:text-slate-700">{{ __('Governorates') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $governorate->name_en }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">{{ __('Cities') }} — {{ $governorate->name_en }}</h1>
            <p class="text-sm text-slate-500">{{ __('Manage the cities that belong to this governorate.') }}</p>
        </div>
        <button type="button" onclick="window.openCityModal()"
            class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add City') }}
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Name (EN)') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Name (AR)') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cities as $city)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-sm font-medium text-slate-800">{{ $city->name_en }}</td>
                        <td class="px-3 py-2 text-sm text-slate-600" dir="rtl">{{ $city->name_ar }}</td>
                        <td class="px-3 py-2 text-end whitespace-nowrap">
                            <button type="button"
                                onclick="window.openCityModal({{ $city->id }}, @js($city->name_en), @js($city->name_ar))"
                                class="text-slate-400 hover:text-amber-600 me-2">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" @click="confirmAdminDelete(@js(route('admin.governorates.cities.destroy', [$governorate, $city])))" class="text-slate-400 hover:text-red-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-slate-400">{{ __('No cities found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-admin.modal id="city-modal">
        <x-slot:header>
            <span id="city-modal-title">{{ __('Add City') }}</span>
        </x-slot:header>

        <form id="city-form" method="POST" action="{{ route('admin.governorates.cities.store', $governorate) }}">
            @csrf
            <input type="hidden" id="city-form-method" name="_method" value="POST">

            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (EN)') }}</label>
                <input type="text" name="name_en" id="city-form-name-en" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (AR)') }}</label>
                <input type="text" name="name_ar" id="city-form-name-ar" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal-city-modal'))" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </x-admin.modal>
@endsection

@push('scripts')
    <script>
        window.openCityModal = function (id, nameEn, nameAr) {
            const form = document.getElementById('city-form');
            const methodInput = document.getElementById('city-form-method');
            const title = document.getElementById('city-modal-title');

            document.getElementById('city-form-name-en').value = nameEn ?? '';
            document.getElementById('city-form-name-ar').value = nameAr ?? '';

            if (id) {
                form.action = @js(route('admin.governorates.cities.update', [$governorate, '__ID__'])).replace('__ID__', id);
                methodInput.value = 'PUT';
                title.textContent = @js(__('Edit City'));
            } else {
                form.action = @js(route('admin.governorates.cities.store', $governorate));
                methodInput.value = 'POST';
                title.textContent = @js(__('Add City'));
            }

            window.dispatchEvent(new CustomEvent('open-modal-city-modal'));
        };
    </script>
@endpush
