@extends('admin.layouts.app')

@php
    $isEdit = $banner->exists;
    $validateUrl = $isEdit ? route('admin.banners.update.validate', $banner) : route('admin.banners.validate');
    $submitUrl = $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store');
@endphp

@section('title', $isEdit ? __('Edit Banner') : __('Create Banner'))

@section('breadcrumb')
    <a href="{{ route('admin.banners.index') }}" class="hover:text-slate-700">{{ __('Banners') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form id="banner-form" method="POST" enctype="multipart/form-data"
    @submit.prevent="AdminForm.submit('banner-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.banners.index')))">
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
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title (EN)') }}</label>
                            <input type="text" name="title[en]" value="{{ old('title.en', $banner->getTranslation('title', 'en')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </x-slot:en>
                    <x-slot:ar>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title (AR)') }}</label>
                            <input type="text" name="title[ar]" value="{{ old('title.ar', $banner->getTranslation('title', 'ar')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                        </div>
                    </x-slot:ar>
                </x-admin.lang-tabs>
            </x-admin.card>

            <x-admin.card :title="__('Link')">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Type') }}</label>
                        <x-admin.select name="type" :options="['product' => __('Product'), 'category' => __('Category'), 'external' => __('External URL')]" :selected="old('type', $banner->type ?? 'external')" />
                    </div>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('URL / Slug') }}</label>
                        <input type="text" name="url" value="{{ old('url', $banner->url) }}"
                            placeholder="{{ __('Product/category slug, or full URL for external') }}"
                            class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    {{ __('Leave empty to link nowhere. For "Product"/"Category" enter the slug; for "External" enter a full URL.') }}
                </p>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Image')">
                <x-admin.image-upload name="image" :current="$banner->image" />
            </x-admin.card>

            <x-admin.card :title="__('Settings')">
                <div class="admin-field mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
                        class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <x-admin.checkbox name="is_active" :checked="old('is_active', $banner->is_active ?? true)" :label="__('Active')" />
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Banner') : __('Create Banner') }}
                </button>
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
