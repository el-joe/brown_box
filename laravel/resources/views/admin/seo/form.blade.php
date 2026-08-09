@extends('admin.layouts.app')

@section('title', __('SEO').' — '.$title)

@section('breadcrumb')
    <a href="{{ route('admin.seo.index') }}" class="hover:text-slate-700">{{ __('SEO') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $title }}</span>
@endsection

@section('content')
    <div x-data="seoForm({
        titleEn: @js(old('title.en', $seoPage->getTranslation('title', 'en'))),
        titleAr: @js(old('title.ar', $seoPage->getTranslation('title', 'ar'))),
        descriptionEn: @js(old('description.en', $seoPage->getTranslation('description', 'en'))),
        canonicalUrl: @js(old('canonical_url', $seoPage->canonical_url)),
    })">
        <form method="POST" action="{{ $submitUrl }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-admin.card :title="__('Meta Tags')">
                        <x-admin.lang-tabs>
                            <x-slot:en>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title (EN)') }}</label>
                                    <input type="text" name="title[en]" x-model="titleEn" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description (EN)') }}</label>
                                    <textarea name="description[en]" x-model="descriptionEn" rows="3" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Keywords (EN)') }}</label>
                                    <input type="text" name="keywords[en]" value="{{ old('keywords.en', $seoPage->getTranslation('keywords', 'en')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                            </x-slot:en>
                            <x-slot:ar>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title (AR)') }}</label>
                                    <input type="text" name="title[ar]" value="{{ old('title.ar', $seoPage->getTranslation('title', 'ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description (AR)') }}</label>
                                    <textarea name="description[ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('description.ar', $seoPage->getTranslation('description', 'ar')) }}</textarea>
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Keywords (AR)') }}</label>
                                    <input type="text" name="keywords[ar]" value="{{ old('keywords.ar', $seoPage->getTranslation('keywords', 'ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                                </div>
                            </x-slot:ar>
                        </x-admin.lang-tabs>
                    </x-admin.card>

                    <x-admin.card :title="__('Open Graph')">
                        <x-admin.lang-tabs>
                            <x-slot:en>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Title (EN)') }}</label>
                                    <input type="text" name="og_title[en]" value="{{ old('og_title.en', $seoPage->getTranslation('og_title', 'en')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Description (EN)') }}</label>
                                    <textarea name="og_description[en]" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('og_description.en', $seoPage->getTranslation('og_description', 'en')) }}</textarea>
                                </div>
                            </x-slot:en>
                            <x-slot:ar>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Title (AR)') }}</label>
                                    <input type="text" name="og_title[ar]" value="{{ old('og_title.ar', $seoPage->getTranslation('og_title', 'ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Description (AR)') }}</label>
                                    <textarea name="og_description[ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('og_description.ar', $seoPage->getTranslation('og_description', 'ar')) }}</textarea>
                                </div>
                            </x-slot:ar>
                        </x-admin.lang-tabs>

                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Image') }}</label>
                            <x-admin.image-upload name="og_image" :current="$seoPage->og_image" />
                        </div>
                    </x-admin.card>

                    <x-admin.card :title="__('Advanced')">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Schema JSON') }}</label>
                            <textarea name="schema_json" rows="6" class="w-full rounded-lg border-slate-300 text-sm font-mono"
                                x-data
                                @blur="try { if ($el.value.trim()) { JSON.parse($el.value); $el.classList.remove('border-red-400'); } } catch (e) { $el.classList.add('border-red-400'); }"
                            >{{ old('schema_json', $seoPage->schema_json ? json_encode($seoPage->schema_json, JSON_PRETTY_PRINT) : '') }}</textarea>
                            <p class="text-xs text-slate-400 mt-1">{{ __('Must be valid JSON, e.g. structured data (JSON-LD).') }}</p>
                        </div>
                    </x-admin.card>
                </div>

                <div class="space-y-6">
                    <x-admin.card :title="__('Google Preview')">
                        <div class="border border-slate-200 rounded-lg p-3">
                            <div class="text-xs text-slate-500 truncate" x-text="canonicalUrl || @js(url('/'))"></div>
                            <div class="text-lg text-blue-800 truncate" x-text="titleEn || @js($title)"></div>
                            <div class="text-sm text-slate-600 line-clamp-2" x-text="descriptionEn || @js(__('No description provided.'))"></div>
                        </div>
                    </x-admin.card>

                    <x-admin.card :title="__('Indexing')">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Canonical URL') }}</label>
                            <input type="url" name="canonical_url" x-model="canonicalUrl" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>

                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Robots') }}</label>
                            <x-admin.select name="robots" :options="collect(\App\Enums\Robots::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])" :selected="old('robots', $seoPage->robots ?? \App\Enums\Robots::IndexFollow->value)" />
                        </div>
                    </x-admin.card>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                            {{ __('Save SEO Settings') }}
                        </button>
                        <a href="{{ route('admin.seo.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function seoForm({ titleEn, titleAr, descriptionEn, canonicalUrl }) {
            return { titleEn, titleAr, descriptionEn, canonicalUrl };
        }
    </script>
@endpush
