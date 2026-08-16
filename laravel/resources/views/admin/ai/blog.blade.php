@extends('admin.layouts.app')

@section('title', __('AI Blog Generator'))

@section('breadcrumb')
    <a href="{{ route('admin.ai.dashboard') }}">{{ __('AI Module') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Blog Generator') }}</span>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        x-data="{
            ...aiProviderSelector('{{ array_key_first($activeProviders) ?? '' }}'),
            topic: '',
            locale: 'en',
            tone: 'friendly',
            categoryIds: [],
            wordCount: 800,
            loading: false,
            result: null,
            error: null,
            saving: false,
            showSource: false,
            generate() {
                this.loading = true;
                this.error = null;
                fetch(@js(route('admin.ai.blog.generate')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        provider: this.provider, model: this.model, locale: this.locale,
                        topic: this.topic, tone: this.tone, word_count: this.wordCount,
                        category_ids: this.categoryIds,
                    }),
                })
                    .then(r => r.json())
                    .then(d => { this.result = d.data; this.loading = false; })
                    .catch(() => { this.error = '{{ __('Something went wrong.') }}'; this.loading = false; });
            },
            save() {
                this.saving = true;
                fetch(@js(route('admin.ai.blog.save')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        locale: this.locale,
                        title: this.result.title,
                        excerpt: this.result.excerpt,
                        content: this.result.content,
                        meta_title: this.result.meta_title,
                        meta_description: this.result.meta_description,
                        blog_category_id: this.result.blog_category_id || null,
                    }),
                })
                    .then(r => r.json())
                    .then(d => { this.saving = false; if (d.success) window.location.href = d.edit_url; });
            },
        }">
        <x-admin.card :title="__('Generate Blog Post')">
            @include('admin.ai._provider_selector')

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Topic') }}</label>
                <textarea rows="2" x-model="topic" placeholder="{{ __('e.g. Top 10 gift ideas for summer') }}" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
            </div>

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Language') }}</label>
                <div class="flex gap-1 text-sm">
                    <button type="button" @click="locale = 'en'" class="px-3 py-1 rounded" :class="locale === 'en' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">EN</button>
                    <button type="button" @click="locale = 'ar'" class="px-3 py-1 rounded" :class="locale === 'ar' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">AR</button>
                </div>
            </div>

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Tone') }}</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach (['friendly' => __('Friendly'), 'professional' => __('Professional'), 'playful' => __('Playful')] as $value => $label)
                        <button type="button" @click="tone = '{{ $value }}'" class="px-3 py-2 rounded-lg border text-xs" :class="tone === '{{ $value }}' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-slate-200 text-slate-500'">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Related Categories') }}</label>
                <div class="max-h-32 overflow-y-auto border border-slate-200 rounded-lg p-2 space-y-1">
                    @foreach ($categories as $category)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" value="{{ $category->id }}" x-model="categoryIds">
                            {{ $category->getTranslation('name', 'en') }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="admin-field mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Word Count') }}: <span x-text="wordCount"></span></label>
                <input type="range" min="300" max="2000" step="50" x-model="wordCount" class="w-full">
            </div>

            <button type="button" @click="generate()" :disabled="loading" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 disabled:opacity-50">
                <i class="fa-solid fa-spinner fa-spin" x-show="loading"></i>
                <span x-text="loading ? '{{ __('Generating...') }}' : '{{ __('Generate') }}'"></span>
            </button>
            <p class="text-red-600 text-xs mt-2" x-show="error" x-text="error"></p>
        </x-admin.card>

        <x-admin.card :title="__('Result')">
            <template x-if="!result">
                <p class="text-sm text-slate-400">{{ __('Generated blog post will appear here.') }}</p>
            </template>
            <template x-if="result">
                <div class="space-y-3">
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Title') }}</label>
                        <input type="text" x-model="result.title" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Excerpt') }}</label>
                        <textarea rows="2" x-model="result.excerpt" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <div class="admin-field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-medium text-slate-500">{{ __('Content') }}</label>
                            <button type="button" @click="showSource = !showSource" class="text-xs text-amber-600" x-text="showSource ? '{{ __('Preview') }}' : '{{ __('HTML Source') }}'"></button>
                        </div>
                        <div x-show="!showSource" x-html="result.content" class="prose prose-sm max-w-none border border-slate-200 rounded-lg p-3 max-h-64 overflow-y-auto"></div>
                        <textarea x-show="showSource" rows="10" x-model="result.content" class="w-full rounded-lg border-slate-300 text-sm font-mono"></textarea>
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Meta Title') }}</label>
                        <input type="text" x-model="result.meta_title" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Meta Description') }}</label>
                        <textarea rows="2" x-model="result.meta_description" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Blog Category') }}</label>
                        <select x-model="result.blog_category_id" class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="">{{ __('None') }}</option>
                            @foreach ($blogCategories as $blogCategory)
                                <option value="{{ $blogCategory->id }}">{{ $blogCategory->getTranslation('name', 'en') ?? $blogCategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" @click="save()" :disabled="saving" class="w-full px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 disabled:opacity-50">
                        {{ __('Save as Draft') }}
                    </button>
                </div>
            </template>
        </x-admin.card>
    </div>
@endsection
