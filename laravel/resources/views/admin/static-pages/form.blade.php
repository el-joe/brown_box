@extends('admin.layouts.app')

@section('title', $page->exists ? __('Edit Static Page') : __('Add Static Page'))

@section('breadcrumb')
    <a href="{{ route('admin.static-pages.index') }}" class="hover:text-slate-700">{{ __('Static Pages') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $page->exists ? __('Edit') : __('Add') }}</span>
@endsection

@section('content')
    @php
        $isEdit = $page->exists;
        $validateUrl = $isEdit ? route('admin.static-pages.update.validate', $page) : route('admin.static-pages.validate');
        $submitUrl = $isEdit ? route('admin.static-pages.update', $page) : route('admin.static-pages.store');
    @endphp

    <div x-data="staticPageForm(@js($isEdit))">
        <form id="static-page-form" method="POST"
            @submit.prevent="beforeSubmit(); AdminForm.submit('static-page-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.static-pages.index')))">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-admin.card :title="__('Content')">
                        <x-admin.lang-tabs>
                            <x-slot:en>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title (EN)') }}</label>
                                    <input type="text" name="title[en]" x-model="titleEn" @input="syncSlug()"
                                        value="{{ old('title.en', $page->getTranslation('title', 'en')) }}"
                                        class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Content (EN)') }}</label>
                                    <div id="content-en-editor" style="min-height: 220px;">{!! old('content.en', $page->getTranslation('content', 'en')) !!}</div>
                                    <textarea name="content[en]" data-editor-source="content-en-editor" class="hidden">{{ old('content.en', $page->getTranslation('content', 'en')) }}</textarea>
                                </div>
                            </x-slot:en>
                            <x-slot:ar>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title (AR)') }}</label>
                                    <input type="text" name="title[ar]" x-model="titleAr" @input="syncSlug()"
                                        value="{{ old('title.ar', $page->getTranslation('title', 'ar')) }}"
                                        class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Content (AR)') }}</label>
                                    <div id="content-ar-editor" style="min-height: 220px;" dir="rtl">{!! old('content.ar', $page->getTranslation('content', 'ar')) !!}</div>
                                    <textarea name="content[ar]" data-editor-source="content-ar-editor" class="hidden">{{ old('content.ar', $page->getTranslation('content', 'ar')) }}</textarea>
                                </div>
                            </x-slot:ar>
                        </x-admin.lang-tabs>
                    </x-admin.card>

                    @include('admin.partials.seo-fields', ['seo' => $page->seoPage])
                </div>

                <div class="space-y-6">
                    <x-admin.card :title="__('Settings')">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Slug') }}</label>
                            <input type="text" name="slug" x-model="slug" @input="slugTouched = true" value="{{ old('slug', $page->slug) }}"
                                class="w-full rounded-lg border-slate-300 text-sm font-mono">
                        </div>

                        <div class="mt-4">
                            <x-admin.checkbox name="is_active" :checked="old('is_active', $page->is_active ?? true)" :label="__('Active')" />
                        </div>
                    </x-admin.card>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                            {{ $isEdit ? __('Update Page') : __('Create Page') }}
                        </button>
                        <a href="{{ route('admin.static-pages.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <script>
        function staticPageForm(isEdit) {
            return {
                titleEn: @js(old('title.en', $page->getTranslation('title', 'en'))),
                titleAr: @js(old('title.ar', $page->getTranslation('title', 'ar'))),
                slug: @js(old('slug', $page->slug)),
                slugTouched: isEdit,

                init() {
                    ['en', 'ar'].forEach((locale) => {
                        const el = document.getElementById(`content-${locale}-editor`);
                        if (!el || !window.Quill) return;
                        const quill = new Quill(el, { theme: 'snow' });
                        el.__quill = quill;
                    });
                },

                syncSlug() {
                    if (this.slugTouched || !this.titleAr) return;
                    this.slug = this.slugify(this.titleAr);
                },

                slugify(value) {
                    return value
                        .toString()
                        .trim()
                        .toLowerCase()
                        .replace(/\s+/g, '-')
                        .replace(/[^\w؀-ۿ-]+/g, '')
                        .replace(/-+/g, '-');
                },

                beforeSubmit() {
                    ['en', 'ar'].forEach((locale) => {
                        const el = document.getElementById(`content-${locale}-editor`);
                        const target = document.querySelector(`[data-editor-source="content-${locale}-editor"]`);
                        if (el && el.__quill && target) {
                            target.value = el.__quill.root.innerHTML;
                        }
                    });
                },
            };
        }
    </script>
@endpush
