@extends('admin.layouts.app')

@php
    $isEdit = $post->exists;
    $validateUrl = $isEdit ? route('admin.blog.update.validate', $post) : route('admin.blog.validate');
    $submitUrl = $isEdit ? route('admin.blog.update', $post) : route('admin.blog.store');
@endphp

@section('title', $isEdit ? __('Edit Post') : __('Create Post'))

@section('breadcrumb')
    <a href="{{ route('admin.blog.index') }}" class="hover:text-slate-700">{{ __('Blog') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form id="blog-post-form" method="POST" enctype="multipart/form-data"
    @submit.prevent="AdminForm.submit('blog-post-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.blog.index')))">
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
                            <input type="text" name="title[en]" value="{{ old('title.en', $post->getTranslation('title', 'en')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Excerpt (EN)') }}</label>
                            <textarea name="excerpt[en]" rows="2" class="w-full rounded-lg border-slate-300 text-sm">{{ old('excerpt.en', $post->getTranslation('excerpt', 'en')) }}</textarea>
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Content (EN)') }}</label>
                            <textarea name="content[en]" rows="10" class="w-full rounded-lg border-slate-300 text-sm">{{ old('content.en', $post->getTranslation('content', 'en')) }}</textarea>
                        </div>
                    </x-slot:en>
                    <x-slot:ar>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title (AR)') }}</label>
                            <input type="text" name="title[ar]" value="{{ old('title.ar', $post->getTranslation('title', 'ar')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Excerpt (AR)') }}</label>
                            <textarea name="excerpt[ar]" rows="2" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('excerpt.ar', $post->getTranslation('excerpt', 'ar')) }}</textarea>
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Content (AR)') }}</label>
                            <textarea name="content[ar]" rows="10" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('content.ar', $post->getTranslation('content', 'ar')) }}</textarea>
                        </div>
                    </x-slot:ar>
                </x-admin.lang-tabs>
            </x-admin.card>

            <x-admin.card :title="__('SEO')">
                <x-admin.lang-tabs>
                    <x-slot:en>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Title (EN)') }}</label>
                            <input type="text" name="meta_title[en]" value="{{ old('meta_title.en', $post->getTranslation('meta_title', 'en')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Description (EN)') }}</label>
                            <textarea name="meta_description[en]" rows="2" class="w-full rounded-lg border-slate-300 text-sm">{{ old('meta_description.en', $post->getTranslation('meta_description', 'en')) }}</textarea>
                        </div>
                    </x-slot:en>
                    <x-slot:ar>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Title (AR)') }}</label>
                            <input type="text" name="meta_title[ar]" value="{{ old('meta_title.ar', $post->getTranslation('meta_title', 'ar')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Description (AR)') }}</label>
                            <textarea name="meta_description[ar]" rows="2" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('meta_description.ar', $post->getTranslation('meta_description', 'ar')) }}</textarea>
                        </div>
                    </x-slot:ar>
                </x-admin.lang-tabs>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Thumbnail')">
                <x-admin.image-upload name="thumbnail" :current="$post->thumbnail" />
            </x-admin.card>

            <x-admin.card :title="__('Organization')">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Category') }}</label>
                    <x-admin.select name="blog_category_id" :options="$categories->pluck('name', 'id')" :selected="old('blog_category_id', $post->blog_category_id)" :placeholder="__('None')" />
                </div>
                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Published At') }}</label>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-lg border-slate-300 text-sm">
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Status')">
                <x-admin.checkbox name="is_published" :checked="old('is_published', $post->is_published ?? false)" :label="__('Published')" />
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Post') : __('Create Post') }}
                </button>
                <a href="{{ route('admin.blog.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
