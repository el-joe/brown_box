@extends('admin.layouts.app')

@section('title', __('Static Pages'))

@section('breadcrumb')
    <span>{{ __('Static Pages') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Static Pages') }}</h1>
        <a href="{{ route('admin.static-pages.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Page') }}
        </a>
    </div>

    <x-admin.filter-card class="mb-6">
        <form method="GET" action="{{ route('admin.static-pages.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Search') }}</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                <x-admin.select name="is_active" :options="[1 => __('Active'), 0 => __('Inactive')]" :selected="$filters['is_active'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Slug') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Title (EN)') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Title (AR)') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Last Updated') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Status') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pages as $page)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-slate-600 font-mono text-xs">{{ $page->slug }}</td>
                        <td class="px-3 py-2 text-slate-800 font-medium">{{ $page->getTranslation('title', 'en') }}</td>
                        <td class="px-3 py-2 text-slate-600" dir="rtl">{{ $page->getTranslation('title', 'ar') }}</td>
                        <td class="px-3 py-2 text-slate-500">{{ $page->updated_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2">
                            <button type="button"
                                x-data="{ active: {{ $page->is_active ? 'true' : 'false' }} }"
                                @click="fetch(@js(route('admin.static-pages.toggle-active', $page)), { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(r => r.json()).then(d => active = d.is_active)"
                                class="px-2 py-1 rounded-full text-xs font-medium"
                                :class="active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                                <span x-text="active ? @js(__('Active')) : @js(__('Inactive'))"></span>
                            </button>
                        </td>
                        <td class="px-3 py-2 text-end whitespace-nowrap">
                            <a href="{{ route('admin.static-pages.edit', $page) }}" class="text-slate-400 hover:text-amber-600 me-2">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" @click="confirmAdminDelete(@js(route('admin.static-pages.destroy', $page)))" class="text-slate-400 hover:text-red-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-400">{{ __('No static pages found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pages->links() }}
    </div>
@endsection
