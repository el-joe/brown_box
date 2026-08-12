@extends('admin.layouts.app')

@php
    $isEdit = $category->exists;
    $validateUrl = $isEdit ? route('admin.blog-categories.update.validate', $category) : route('admin.blog-categories.validate');
    $submitUrl = $isEdit ? route('admin.blog-categories.update', $category) : route('admin.blog-categories.store');
@endphp

@section('title', $isEdit ? __('Edit Blog Category') : __('Create Blog Category'))

@section('breadcrumb')
    <a href="{{ route('admin.blog-categories.index') }}" class="hover:text-slate-700">{{ __('Blog Categories') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form id="blog-category-form" method="POST"
    @submit.prevent="AdminForm.submit('blog-category-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.blog-categories.index')))">
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
                            <input type="text" name="name[en]" value="{{ old('name.en', $category->getTranslation('name', 'en')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </x-slot:en>
                    <x-slot:ar>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name[ar]" value="{{ old('name.ar', $category->getTranslation('name', 'ar')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                        </div>
                    </x-slot:ar>
                </x-admin.lang-tabs>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Status')">
                <x-admin.checkbox name="is_active" :checked="old('is_active', $category->is_active ?? true)" :label="__('Active')" />
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Category') : __('Create Category') }}
                </button>
                <a href="{{ route('admin.blog-categories.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
