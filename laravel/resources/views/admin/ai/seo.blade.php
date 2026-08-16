@extends('admin.layouts.app')

@section('title', __('AI SEO Enhancement'))

@section('breadcrumb')
    <a href="{{ route('admin.ai.dashboard') }}">{{ __('AI Module') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('SEO Enhancement') }}</span>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        x-data="{
            ...aiProviderSelector('{{ array_key_first($activeProviders) ?? '' }}'),
            targetType: 'product',
            targetId: '',
            pageKey: 'home',
            locale: 'en',
            loading: false,
            result: null,
            error: null,
            generate() {
                this.loading = true;
                this.error = null;
                fetch(@js(route('admin.ai.seo.generate')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        provider: this.provider,
                        model: this.model,
                        locale: this.locale,
                        target_type: this.targetType,
                        target_id: this.targetId || null,
                        page_key: this.pageKey,
                    }),
                })
                    .then(r => r.json())
                    .then(d => { this.result = d.data; this.loading = false; })
                    .catch(() => { this.error = '{{ __('Something went wrong.') }}'; this.loading = false; });
            },
            save() {
                fetch(@js(route('admin.ai.seo.save')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        locale: this.locale,
                        target_type: this.targetType,
                        target_id: this.targetId || null,
                        page_key: this.pageKey,
                        meta_title: this.result.meta_title,
                        meta_description: this.result.meta_description,
                        keywords: this.result.keywords,
                        og_title: this.result.og_title,
                        og_description: this.result.og_description,
                    }),
                })
                    .then(r => r.json())
                    .then(d => { if (d.success) alert('{{ __('Saved successfully.') }}'); });
            },
        }">
        <x-admin.card :title="__('Generate SEO Metadata')">
            @include('admin.ai._provider_selector')

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Target') }}</label>
                <div class="flex gap-4 text-sm mb-2">
                    <label class="flex items-center gap-1"><input type="radio" value="product" x-model="targetType"> {{ __('Product') }}</label>
                    <label class="flex items-center gap-1"><input type="radio" value="category" x-model="targetType"> {{ __('Category') }}</label>
                    <label class="flex items-center gap-1"><input type="radio" value="page" x-model="targetType"> {{ __('Page') }}</label>
                </div>

                <select x-show="targetType === 'product'" x-model="targetId" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('Select a product') }}</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->getTranslation('name', 'en') }}</option>
                    @endforeach
                </select>

                <select x-show="targetType === 'category'" x-model="targetId" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('Select a category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->getTranslation('name', 'en') }}</option>
                    @endforeach
                </select>

                <select x-show="targetType === 'page'" x-model="pageKey" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="home">{{ __('Home') }}</option>
                    <option value="blog">{{ __('Blog') }}</option>
                    <option value="category-list">{{ __('Category List') }}</option>
                    <option value="cart">{{ __('Cart') }}</option>
                    <option value="checkout">{{ __('Checkout') }}</option>
                    <option value="about-us">{{ __('About Us') }}</option>
                    <option value="contact-us">{{ __('Contact Us') }}</option>
                </select>
            </div>

            <div class="admin-field mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Language') }}</label>
                <div class="flex gap-1 text-sm">
                    <button type="button" @click="locale = 'en'" class="px-3 py-1 rounded" :class="locale === 'en' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">EN</button>
                    <button type="button" @click="locale = 'ar'" class="px-3 py-1 rounded" :class="locale === 'ar' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">AR</button>
                </div>
            </div>

            <button type="button" @click="generate()" :disabled="loading" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 disabled:opacity-50">
                <i class="fa-solid fa-spinner fa-spin" x-show="loading"></i>
                <span x-text="loading ? '{{ __('Generating...') }}' : '{{ __('Generate') }}'"></span>
            </button>
            <p class="text-red-600 text-xs mt-2" x-show="error" x-text="error"></p>
        </x-admin.card>

        <x-admin.card :title="__('Result')">
            <template x-if="!result">
                <p class="text-sm text-slate-400">{{ __('Generated metadata will appear here.') }}</p>
            </template>
            <template x-if="result">
                <div class="space-y-3">
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Meta Title') }} (<span x-text="(result.meta_title || '').length"></span>/60)</label>
                        <input type="text" x-model="result.meta_title" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Meta Description') }} (<span x-text="(result.meta_description || '').length"></span>/160)</label>
                        <textarea rows="3" x-model="result.meta_description" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Keywords') }}</label>
                        <input type="text" x-model="result.keywords" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('OG Title') }}</label>
                        <input type="text" x-model="result.og_title" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('OG Description') }}</label>
                        <textarea rows="2" x-model="result.og_description" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="save()" class="flex-1 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">{{ __('Save') }}</button>
                        <button type="button" @click="generate()" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium hover:bg-slate-50">{{ __('Regenerate') }}</button>
                    </div>
                </div>
            </template>
        </x-admin.card>
    </div>
@endsection
