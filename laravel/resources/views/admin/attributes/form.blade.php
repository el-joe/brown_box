@extends('admin.layouts.app')

@php
    $isEdit = $attribute->exists;
    $validateUrl = $isEdit ? route('admin.attributes.update.validate', $attribute) : route('admin.attributes.validate');
    $submitUrl = $isEdit ? route('admin.attributes.update', $attribute) : route('admin.attributes.store');

    $initialValues = $isEdit
        ? $attribute->values->map(fn ($value) => [
            'key' => 'value-'.$value->id,
            'id' => $value->id,
            'value_en' => $value->getTranslation('value', 'en'),
            'value_ar' => $value->getTranslation('value', 'ar'),
            'extra_price' => (float) $value->extra_price,
        ])->values()
        : collect();
@endphp

@section('title', $isEdit ? __('Edit Attribute') : __('Create Attribute'))

@section('breadcrumb')
    <a href="{{ route('admin.attributes.index') }}" class="hover:text-slate-700">{{ __('Attributes') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form
    id="attribute-form"
    method="POST"
    x-data="attributeForm({ initialValues: @js($initialValues) })"
    x-init="init()"
    @submit.prevent="AdminForm.submit('attribute-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.attributes.index')))"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('General')">
                <x-admin.lang-tabs>
                    <x-slot:en>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name[en]" value="{{ old('name.en', $attribute->getTranslation('name', 'en')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                            @error('name.en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </x-slot:en>
                    <x-slot:ar>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name[ar]" value="{{ old('name.ar', $attribute->getTranslation('name', 'ar')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                            @error('name.ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </x-slot:ar>
                </x-admin.lang-tabs>
            </x-admin.card>

            <x-admin.card :title="__('Values')">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-2 py-2 text-start w-1/3">{{ __('Value (EN)') }}</th>
                                <th class="px-2 py-2 text-start w-1/3">{{ __('Value (AR)') }}</th>
                                <th class="px-2 py-2 text-start w-32">{{ __('Extra Price') }}</th>
                                <th class="px-2 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in values" :key="row.key">
                                <tr class="border-t border-slate-100 align-top">
                                    <td class="px-2 py-2">
                                        <input type="hidden" :name="'values['+index+'][id]'" :value="row.id">
                                        <input type="text" :name="'values['+index+'][value][en]'" x-model="row.value_en" class="w-full rounded-lg border-slate-300 text-sm">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" :name="'values['+index+'][value][ar]'" x-model="row.value_ar" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" min="0" step="0.01" :name="'values['+index+'][extra_price]'" x-model.number="row.extra_price" class="w-28 rounded-lg border-slate-300 text-sm">
                                    </td>
                                    <td class="px-2 py-2 text-end">
                                        <button type="button" @click="removeValue(index)" class="text-slate-400 hover:text-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!values.length">
                                <td colspan="4" class="px-2 py-6 text-center text-slate-400">{{ __('No values yet — add one below.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" @click="addValue()" class="mt-4 px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">
                    <i class="fa-solid fa-plus me-1"></i>{{ __('Add Value') }}
                </button>
                @error('values')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Attribute') : __('Create Attribute') }}
                </button>
                <a href="{{ route('admin.attributes.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
    <script>
        function attributeForm({ initialValues }) {
            return {
                values: [],

                init() {
                    this.values = (initialValues || []).map((value) => ({ ...value }));
                },

                addValue() {
                    this.values.push({
                        key: Date.now() + Math.random(),
                        id: '',
                        value_en: '',
                        value_ar: '',
                        extra_price: 0,
                    });
                },

                removeValue(index) {
                    this.values.splice(index, 1);
                },
            };
        }
    </script>
@endpush
