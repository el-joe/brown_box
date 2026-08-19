@extends('admin.layouts.app')

@section('title', __('SEO'))

@section('breadcrumb')
    <span>{{ __('SEO') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('SEO') }}</h1>
        <button type="button" @click="$dispatch('open-modal-bulk-seo')" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-wand-magic-sparkles me-1"></i>{{ __('Edit All Products SEO') }}
        </button>
    </div>

    <div class="space-y-6">
        <x-admin.card :title="__('Static Pages')">
            <div class="divide-y divide-slate-100">
                @foreach ($staticPages as $item)
                    <a href="{{ route('admin.seo.page.edit', $item['page_key']) }}" class="flex items-center justify-between py-3 hover:bg-slate-50 px-2 -mx-2 rounded-lg">
                        <span class="text-sm text-slate-700">{{ __($item['label']) }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item['filled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item['filled'] ? __('Filled') : __('Empty') }}
                        </span>
                    </a>
                @endforeach
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Products')">
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse ($products as $item)
                    <a href="{{ route('admin.seo.model.edit', ['product', $item['id']]) }}" class="flex items-center justify-between py-3 hover:bg-slate-50 px-2 -mx-2 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $item['name'] }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item['filled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item['filled'] ? __('Filled') : __('Empty') }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400 py-3">{{ __('No products found.') }}</p>
                @endforelse
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Categories')">
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse ($categories as $item)
                    <a href="{{ route('admin.seo.model.edit', ['category', $item['id']]) }}" class="flex items-center justify-between py-3 hover:bg-slate-50 px-2 -mx-2 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $item['name'] }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item['filled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item['filled'] ? __('Filled') : __('Empty') }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400 py-3">{{ __('No categories found.') }}</p>
                @endforelse
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Blog Posts')">
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse ($blogPosts as $item)
                    <a href="{{ route('admin.seo.model.edit', ['blog_post', $item['id']]) }}" class="flex items-center justify-between py-3 hover:bg-slate-50 px-2 -mx-2 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $item['name'] }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item['filled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item['filled'] ? __('Filled') : __('Empty') }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400 py-3">{{ __('No blog posts found.') }}</p>
                @endforelse
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Brands')">
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse ($brands as $item)
                    <a href="{{ route('admin.seo.model.edit', ['brand', $item['id']]) }}" class="flex items-center justify-between py-3 hover:bg-slate-50 px-2 -mx-2 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $item['name'] }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item['filled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item['filled'] ? __('Filled') : __('Empty') }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400 py-3">{{ __('No brands found.') }}</p>
                @endforelse
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Static Pages (Content)')">
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse ($staticModels as $item)
                    <a href="{{ route('admin.seo.model.edit', ['static_page', $item['id']]) }}" class="flex items-center justify-between py-3 hover:bg-slate-50 px-2 -mx-2 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $item['name'] }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item['filled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item['filled'] ? __('Filled') : __('Empty') }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400 py-3">{{ __('No static pages found.') }}</p>
                @endforelse
            </div>
        </x-admin.card>
    </div>

    <x-admin.modal id="bulk-seo" size="lg" :header="__('Edit All Products SEO')">
        <form id="bulk-seo-form" method="POST" action="{{ route('admin.seo.products.bulk') }}">
            @csrf
            <p class="text-xs text-slate-500 mb-4">
                {{ __('Applies this template to every product that currently has no SEO data. Use {name} as a placeholder for the product name.') }}
            </p>

            <x-admin.lang-tabs>
                <x-slot:en>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title Template (EN)') }}</label>
                        <input type="text" name="title[en]" placeholder="{name} | Brown Box" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description Template (EN)') }}</label>
                        <textarea name="description[en]" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ __('Buy {name} at the best price.') }}</textarea>
                    </div>
                </x-slot:en>
                <x-slot:ar>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Title Template (AR)') }}</label>
                        <input type="text" name="title[ar]" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                    </div>
                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description Template (AR)') }}</label>
                        <textarea name="description[ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl"></textarea>
                    </div>
                </x-slot:ar>
            </x-admin.lang-tabs>
        </form>
        <x-slot:footer>
            <button type="submit" form="bulk-seo-form" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                {{ __('Apply') }}
            </button>
        </x-slot:footer>
    </x-admin.modal>
@endsection
