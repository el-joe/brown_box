@extends('admin.layouts.app')

@section('title', __('Expenses'))

@section('breadcrumb')
    <span>{{ __('Expenses') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Expenses') }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.expense-categories.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-sitemap me-1"></i>{{ __('Categories') }}
            </a>
            <a href="{{ route('admin.expenses.export', request()->query()) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-file-excel me-1"></i>{{ __('Export') }}
            </a>
            <a href="{{ route('admin.expenses.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                <i class="fa-solid fa-plus me-1"></i>{{ __('Add Expense') }}
            </a>
        </div>
    </div>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Category') }}</label>
                <x-admin.select name="category_id" :options="$categories" :selected="$filters['category_id'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Payment Method') }}</label>
                <x-admin.select name="payment_method" :options="['cash' => __('Cash'), 'bank' => __('Bank'), 'other' => __('Other')]" :selected="$filters['payment_method'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Date From') }}</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Date To') }}</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Min Amount') }}</label>
                <input type="number" step="0.01" name="min_amount" value="{{ $filters['min_amount'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Max Amount') }}</label>
                <input type="number" step="0.01" name="max_amount" value="{{ $filters['max_amount'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="lg:col-span-6 flex justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <div class="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Date') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Category') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Description') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Amount') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Payment Method') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Reference') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Admin') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenses as $expense)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-slate-600">{{ $expense->date?->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $expense->category?->full_path ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ \Illuminate\Support\Str::limit($expense->description, 60) ?: '—' }}</td>
                        <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $expense->amount) }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ ucfirst($expense->payment_method) }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $expense->reference ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $expense->admin?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-end whitespace-nowrap">
                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-slate-400 hover:text-amber-600 me-2">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" @click="confirmAdminDelete(@js(route('admin.expenses.destroy', $expense)))" class="text-slate-400 hover:text-red-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-slate-400">{{ __('No expenses found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($expenses->isNotEmpty())
                <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="3" class="px-3 py-2 text-end text-sm font-semibold text-slate-700">{{ __('Total (filtered period)') }}</td>
                        <td class="px-3 py-2 text-end text-sm font-bold text-slate-900">{{ money_format((float) $total) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
@endsection
