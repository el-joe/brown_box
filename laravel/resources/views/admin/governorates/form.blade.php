@extends('admin.layouts.app')

@php $isEdit = $governorate->exists; @endphp

@section('title', $isEdit ? __('Edit Governorate') : __('Add Governorate'))

@section('breadcrumb')
    <a href="{{ route('admin.governorates.index') }}" class="hover:text-slate-700">{{ __('Governorates') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Add') }}</span>
@endsection

@section('content')
    <div class="mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ $isEdit ? __('Edit Governorate') : __('Add Governorate') }}</h1>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.governorates.update', $governorate) : route('admin.governorates.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('General')">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (EN)') }}</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $governorate->name_en) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    @error('name_en')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (AR)') }}</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $governorate->name_ar) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                    @error('name_ar')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Country') }}</label>
                    <x-admin.select name="country_id" :options="$countries->pluck('name_en', 'id')" :selected="old('country_id', $governorate->country_id ?? $countries->first()?->id)" />
                    @error('country_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Governorate') : __('Create Governorate') }}
                </button>
                <a href="{{ route('admin.governorates.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </form>
@endsection
