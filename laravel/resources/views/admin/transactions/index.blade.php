@extends('admin.layouts.app')

@section('title', __('Transactions'))

@section('breadcrumb')
    <span>{{ __('Transactions') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Transaction Ledger') }}</h1>
    </div>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Type') }}</label>
                <x-admin.select name="type" :options="['debit' => __('Debit'), 'credit' => __('Credit')]" :selected="$filters['type'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Model') }}</label>
                <x-admin.select name="model_type" :options="$modelTypes" :selected="$filters['model_type'] ?? null" :placeholder="__('All')" />
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
                    <th class="px-3 py-2 text-start">{{ __('Type') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Model') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Amount') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Balance Before') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Balance After') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Description') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-slate-600">{{ $transaction->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $transaction->type === 'debit' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $transaction->type === 'debit' ? __('Debit') : __('Credit') }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-slate-600">{{ class_basename($transaction->model_type) }} #{{ $transaction->model_id }}</td>
                        <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $transaction->amount) }}</td>
                        <td class="px-3 py-2 text-end text-slate-500">{{ money_format((float) $transaction->balance_before) }}</td>
                        <td class="px-3 py-2 text-end text-slate-500">{{ money_format((float) $transaction->balance_after) }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $transaction->description ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-slate-400">{{ __('No transactions found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
@endsection
