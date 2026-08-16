@extends('admin.layouts.app')

@section('title', __('Opening Balances'))

@section('breadcrumb')
    <a href="{{ route('admin.accounting.index') }}" class="hover:text-slate-700">{{ __('Accounting') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Opening Balances') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Opening Balances') }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-admin.card :title="__('Cash Account')">
            <form method="POST" action="{{ route('admin.opening-balances.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="entity_type" value="cash">
                <div class="text-xs text-slate-500">{{ __('Current opening balance') }}: <span class="font-semibold text-slate-700">{{ money_format((float) ($cash->amount ?? 0)) }}</span></div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Amount') }}</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $cash->amount ?? 0) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Date') }}</label>
                        <input type="date" name="date" value="{{ old('date', $cash?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Notes') }}</label>
                    <input type="text" name="notes" value="{{ old('notes', $cash->notes ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">{{ __('Save') }}</button>
            </form>
        </x-admin.card>

        <x-admin.card :title="__('Bank Account')">
            <form method="POST" action="{{ route('admin.opening-balances.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="entity_type" value="bank">
                <div class="text-xs text-slate-500">{{ __('Current opening balance') }}: <span class="font-semibold text-slate-700">{{ money_format((float) ($bank->amount ?? 0)) }}</span></div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Amount') }}</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $bank->amount ?? 0) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Date') }}</label>
                        <input type="date" name="date" value="{{ old('date', $bank?->date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Notes') }}</label>
                    <input type="text" name="notes" value="{{ old('notes', $bank->notes ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">{{ __('Save') }}</button>
            </form>
        </x-admin.card>
    </div>

    <div class="mt-6">
        <x-admin.card :title="__('Affiliate Balances')">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-2 text-start">{{ __('Affiliate') }}</th>
                            <th class="px-3 py-2 text-end">{{ __('Opening Balance') }}</th>
                            <th class="px-3 py-2 text-start">{{ __('Date') }}</th>
                            <th class="px-3 py-2 text-start">{{ __('Notes') }}</th>
                            <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($affiliates as $affiliate)
                            @php $ob = $affiliateBalances->get($affiliate->id); @endphp
                            <tr class="border-t border-slate-100" x-data="{ open: false }">
                                <td class="px-3 py-2 text-slate-700">{{ $affiliate->customer?->name ?? $affiliate->code }}</td>
                                <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) ($ob->amount ?? 0)) }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $ob?->date?->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $ob->notes ?? '—' }}</td>
                                <td class="px-3 py-2 text-end">
                                    <button type="button" @click="open = !open" class="text-amber-600 hover:text-amber-700 text-xs font-medium">{{ __('Set Balance') }}</button>
                                </td>
                            </tr>
                            <tr x-show="open" x-cloak>
                                <td colspan="5" class="px-3 py-3 bg-slate-50">
                                    <form method="POST" action="{{ route('admin.opening-balances.store') }}" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        <input type="hidden" name="entity_type" value="affiliate">
                                        <input type="hidden" name="affiliate_id" value="{{ $affiliate->id }}">
                                        <div class="admin-field">
                                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Amount') }}</label>
                                            <input type="number" step="0.01" name="amount" value="{{ $ob->amount ?? 0 }}" class="rounded-lg border-slate-300 text-sm">
                                        </div>
                                        <div class="admin-field">
                                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Date') }}</label>
                                            <input type="date" name="date" value="{{ $ob->date?->format('Y-m-d') ?? now()->format('Y-m-d') }}" class="rounded-lg border-slate-300 text-sm">
                                        </div>
                                        <div class="admin-field flex-1">
                                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Notes') }}</label>
                                            <input type="text" name="notes" value="{{ $ob->notes ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                                        </div>
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">{{ __('Save') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-400">{{ __('No affiliates found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    </div>
@endsection
