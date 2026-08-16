@extends('admin.layouts.app')

@section('title', __('AI Product Descriptions'))

@section('breadcrumb')
    <a href="{{ route('admin.ai.dashboard') }}">{{ __('AI Module') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Product Descriptions') }}</span>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        x-data="{
            ...aiProviderSelector('{{ array_key_first($activeProviders) ?? '' }}'),
            productId: '',
            locale: 'en',
            tone: 'persuasive',
            loading: false,
            saving: false,
            result: null,
            error: null,
            showSource: false,
            generate() {
                this.loading = true;
                this.error = null;
                fetch(@js(route('admin.ai.product-description.generate')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ provider: this.provider, model: this.model, locale: this.locale, product_id: this.productId, tone: this.tone }),
                })
                    .then(r => r.json())
                    .then(d => { this.result = d.data; this.loading = false; })
                    .catch(() => { this.error = '{{ __('Something went wrong.') }}'; this.loading = false; });
            },
            save() {
                this.saving = true;
                fetch(@js(route('admin.ai.product-description.save')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: this.productId, locale: this.locale,
                        description: this.result.description, short_description: this.result.short_description,
                    }),
                })
                    .then(r => r.json())
                    .then(d => { this.saving = false; if (d.success) alert('{{ __('Saved successfully.') }}'); });
            },
        }">
        <x-admin.card :title="__('Generate Description')">
            @include('admin.ai._provider_selector')

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Product') }}</label>
                <select x-model="productId" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('Select a product') }}</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->getTranslation('name', 'en') }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Language') }}</label>
                <div class="flex gap-1 text-sm">
                    <button type="button" @click="locale = 'en'" class="px-3 py-1 rounded" :class="locale === 'en' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">EN</button>
                    <button type="button" @click="locale = 'ar'" class="px-3 py-1 rounded" :class="locale === 'ar' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">AR</button>
                </div>
            </div>

            <div class="admin-field mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Tone') }}</label>
                <select x-model="tone" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="persuasive">{{ __('Persuasive') }}</option>
                    <option value="professional">{{ __('Professional') }}</option>
                    <option value="minimal">{{ __('Minimal') }}</option>
                    <option value="luxury">{{ __('Luxury') }}</option>
                </select>
            </div>

            <button type="button" @click="generate()" :disabled="loading || !productId" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 disabled:opacity-50">
                <i class="fa-solid fa-spinner fa-spin" x-show="loading"></i>
                <span x-text="loading ? '{{ __('Generating...') }}' : '{{ __('Generate') }}'"></span>
            </button>
            <p class="text-red-600 text-xs mt-2" x-show="error" x-text="error"></p>
        </x-admin.card>

        <x-admin.card :title="__('Result')">
            <template x-if="!result">
                <p class="text-sm text-slate-400">{{ __('Generated description will appear here.') }}</p>
            </template>
            <template x-if="result">
                <div class="space-y-3">
                    <div class="admin-field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-medium text-slate-500">{{ __('Description') }}</label>
                            <button type="button" @click="showSource = !showSource" class="text-xs text-amber-600" x-text="showSource ? '{{ __('Preview') }}' : '{{ __('HTML Source') }}'"></button>
                        </div>
                        <div x-show="!showSource" x-html="result.description" class="prose prose-sm max-w-none border border-slate-200 rounded-lg p-3 max-h-64 overflow-y-auto"></div>
                        <textarea x-show="showSource" rows="10" x-model="result.description" class="w-full rounded-lg border-slate-300 text-sm font-mono"></textarea>
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Short Description') }} (<span x-text="(result.short_description || '').length"></span> {{ __('chars') }})</label>
                        <textarea rows="2" x-model="result.short_description" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <button type="button" @click="save()" :disabled="saving" class="w-full px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 disabled:opacity-50">
                        {{ __('Save to Product') }}
                    </button>
                </div>
            </template>
        </x-admin.card>
    </div>
@endsection
