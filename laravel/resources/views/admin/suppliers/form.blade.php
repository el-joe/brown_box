@extends('admin.layouts.app')

@php
    $isEdit = $supplier->exists;
    $validateUrl = $isEdit ? route('admin.suppliers.update.validate', $supplier) : route('admin.suppliers.validate');
    $submitUrl = $isEdit ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store');
@endphp

@section('title', $isEdit ? __('Edit Supplier') : __('Create Supplier'))

@section('breadcrumb')
    <a href="{{ route('admin.suppliers.index') }}" class="hover:text-slate-700">{{ __('Suppliers') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form id="supplier-form" method="POST"
    @submit.prevent="AdminForm.submit('supplier-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.suppliers.index')))">
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
                        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>

                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Email') }}</label>
                            <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </div>

                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Address') }}</label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('address', $supplier->address) }}</textarea>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Balance')">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Opening Balance') }}</label>
                    <input type="number" step="0.01" name="balance" value="{{ old('balance', $supplier->balance ?? 0) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Supplier') : __('Create Supplier') }}
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
