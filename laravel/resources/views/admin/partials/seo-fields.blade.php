@php($seo = $seo ?? null)

<x-admin.card :title="__('SEO')">
    <x-admin.lang-tabs>
        <x-slot:en>
            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Title (EN)') }}</label>
                <input type="text" name="seo[title][en]" value="{{ old('seo.title.en', $seo?->getTranslation('title', 'en')) }}"
                    class="w-full rounded-lg border-slate-300 text-sm" maxlength="255">
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Description (EN)') }}</label>
                <textarea name="seo[description][en]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" maxlength="500">{{ old('seo.description.en', $seo?->getTranslation('description', 'en')) }}</textarea>
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Keywords (EN)') }}</label>
                <input type="text" name="seo[keywords][en]" value="{{ old('seo.keywords.en', $seo?->getTranslation('keywords', 'en')) }}"
                    class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Title (EN)') }}</label>
                <input type="text" name="seo[og_title][en]" value="{{ old('seo.og_title.en', $seo?->getTranslation('og_title', 'en')) }}"
                    class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Description (EN)') }}</label>
                <textarea name="seo[og_description][en]" rows="2" class="w-full rounded-lg border-slate-300 text-sm">{{ old('seo.og_description.en', $seo?->getTranslation('og_description', 'en')) }}</textarea>
            </div>
        </x-slot:en>
        <x-slot:ar>
            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Title (AR)') }}</label>
                <input type="text" name="seo[title][ar]" value="{{ old('seo.title.ar', $seo?->getTranslation('title', 'ar')) }}"
                    class="w-full rounded-lg border-slate-300 text-sm" dir="rtl" maxlength="255">
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Description (AR)') }}</label>
                <textarea name="seo[description][ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl" maxlength="500">{{ old('seo.description.ar', $seo?->getTranslation('description', 'ar')) }}</textarea>
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Keywords (AR)') }}</label>
                <input type="text" name="seo[keywords][ar]" value="{{ old('seo.keywords.ar', $seo?->getTranslation('keywords', 'ar')) }}"
                    class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Title (AR)') }}</label>
                <input type="text" name="seo[og_title][ar]" value="{{ old('seo.og_title.ar', $seo?->getTranslation('og_title', 'ar')) }}"
                    class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
            </div>
            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Description (AR)') }}</label>
                <textarea name="seo[og_description][ar]" rows="2" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('seo.og_description.ar', $seo?->getTranslation('og_description', 'ar')) }}</textarea>
            </div>
        </x-slot:ar>
    </x-admin.lang-tabs>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5 pt-5 border-t border-slate-100">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Image') }}</label>
            <x-admin.image-upload name="seo[og_image]" :current="$seo?->og_image" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Canonical URL') }}</label>
            <input type="url" name="seo[canonical_url]" value="{{ old('seo.canonical_url', $seo?->canonical_url) }}"
                class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Robots') }}</label>
            <x-admin.select name="seo[robots]" :options="collect(\App\Enums\Robots::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])" :selected="old('seo.robots', $seo?->robots ?? \App\Enums\Robots::IndexFollow->value)" />
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Schema JSON-LD') }}</label>
            <textarea name="seo[schema_json]" rows="3" class="w-full rounded-lg border-slate-300 text-xs font-mono">{{ old('seo.schema_json', $seo?->schema_json ? json_encode($seo->schema_json, JSON_PRETTY_PRINT) : '') }}</textarea>
        </div>
    </div>
</x-admin.card>
