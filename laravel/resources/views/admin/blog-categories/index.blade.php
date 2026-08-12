@extends('admin.layouts.app')

@section('title', __('Blog Categories'))

@section('breadcrumb')
    <a href="{{ route('admin.blog.index') }}" class="hover:text-slate-700">{{ __('Blog') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Blog Categories') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Blog Categories') }}</h1>
        <a href="{{ route('admin.blog-categories.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Category') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="text-start px-4 py-3">{{ __('Name (EN)') }}</th>
                    <th class="text-start px-4 py-3">{{ __('Name (AR)') }}</th>
                    <th class="text-start px-4 py-3">{{ __('Slug') }}</th>
                    <th class="text-start px-4 py-3">{{ __('Posts') }}</th>
                    <th class="text-start px-4 py-3">{{ __('Status') }}</th>
                    <th class="text-start px-4 py-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3">{{ $category->getTranslation('name', 'en') }}</td>
                        <td class="px-4 py-3">{{ $category->getTranslation('name', 'ar') }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $category->slug }}</td>
                        <td class="px-4 py-3">{{ $category->posts_count }}</td>
                        <td class="px-4 py-3">
                            <button type="button"
                                x-data="{ active: {{ $category->is_active ? 'true' : 'false' }} }"
                                @click="fetch(@js(route('admin.blog-categories.toggle-active', $category)), { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(r => r.json()).then(d => active = d.is_active)"
                                class="px-2 py-1 rounded-full text-xs font-medium"
                                :class="active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                                <span x-text="active ? @js(__('Active')) : @js(__('Inactive'))"></span>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.blog-categories.edit', $category) }}" class="text-slate-400 hover:text-amber-600 me-2">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" @click="confirmAdminDelete(@js(route('admin.blog-categories.destroy', $category)))" class="text-slate-400 hover:text-red-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-400">{{ __('No categories found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
