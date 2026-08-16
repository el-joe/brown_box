@extends('admin.layouts.app')

@section('title', __('AI Trending Research'))

@section('breadcrumb')
    <a href="{{ route('admin.ai.dashboard') }}">{{ __('AI Module') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Trending Research') }}</span>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        x-data="{
            ...aiProviderSelector('{{ array_key_first($activeProviders) ?? '' }}'),
            categoryIds: [],
            market: 'Egypt',
            loading: false,
            result: null,
            error: null,
            generate() {
                this.loading = true;
                this.error = null;
                fetch(@js(route('admin.ai.trending.generate')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ provider: this.provider, model: this.model, category_ids: this.categoryIds, market: this.market }),
                })
                    .then(r => r.json())
                    .then(d => { this.result = d.data; this.loading = false; })
                    .catch(() => { this.error = '{{ __('Something went wrong.') }}'; this.loading = false; });
            },
        }">
        <x-admin.card :title="__('Research Trends')">
            @include('admin.ai._provider_selector')

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Categories') }}</label>
                <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-lg p-2 space-y-1">
                    @foreach ($categories as $category)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" value="{{ $category->id }}" x-model="categoryIds">
                            {{ $category->getTranslation('name', 'en') }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="admin-field mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Market') }}</label>
                <input type="text" x-model="market" class="w-full rounded-lg border-slate-300 text-sm">
            </div>

            <button type="button" @click="generate()" :disabled="loading" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 disabled:opacity-50">
                <i class="fa-solid fa-spinner fa-spin" x-show="loading"></i>
                <span x-text="loading ? '{{ __('Researching...') }}' : '{{ __('Generate') }}'"></span>
            </button>
            <p class="text-red-600 text-xs mt-2" x-show="error" x-text="error"></p>
        </x-admin.card>

        <x-admin.card :title="__('Results')">
            <template x-if="!result">
                <p class="text-sm text-slate-400">{{ __('Research results will appear here.') }}</p>
            </template>
            <template x-if="result">
                <div class="space-y-5">
                    <div>
                        <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">{{ __('Trending Products') }}</h4>
                        <ul class="space-y-1">
                            <template x-for="item in (result.trending_products || [])" :key="item">
                                <li class="text-sm bg-amber-50 text-amber-800 rounded-lg px-3 py-2" x-text="item"></li>
                            </template>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">{{ __('Emerging Niches') }}</h4>
                        <ul class="space-y-1">
                            <template x-for="item in (result.emerging_niches || [])" :key="item">
                                <li class="text-sm bg-emerald-50 text-emerald-800 rounded-lg px-3 py-2" x-text="item"></li>
                            </template>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">{{ __('Seasonal Opportunities') }}</h4>
                        <ul class="space-y-1">
                            <template x-for="item in (result.seasonal_opportunities || [])" :key="item">
                                <li class="text-sm bg-sky-50 text-sky-800 rounded-lg px-3 py-2" x-text="item"></li>
                            </template>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">{{ __('Content Ideas') }}</h4>
                        <ul class="space-y-1">
                            <template x-for="item in (result.content_ideas || [])" :key="item">
                                <li class="text-sm bg-slate-50 text-slate-700 rounded-lg px-3 py-2" x-text="item"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </template>
        </x-admin.card>
    </div>
@endsection
