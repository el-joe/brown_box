@extends('admin.layouts.app')

@section('title', __('Expense Categories'))

@section('breadcrumb')
    <a href="{{ route('admin.expenses.index') }}" class="hover:text-slate-700">{{ __('Expenses') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Categories') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Expense Categories') }}</h1>
        <a href="{{ route('admin.expense-categories.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Category') }}
        </a>
    </div>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.expense-categories.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Type') }}</label>
                <x-admin.select name="type" :options="['debit' => __('Debit'), 'credit' => __('Credit')]" :selected="$filters['type'] ?? null" :placeholder="__('All')" />
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

    <div class="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Name') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Type') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Status') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tree as $node)
                    @include('admin.expense-categories._row', ['nodes' => collect([$node])])
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-slate-400">{{ __('No expense categories found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
