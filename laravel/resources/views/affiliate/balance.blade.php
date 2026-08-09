@extends('affiliate.layouts.app')

@section('title', __('Balance'))
@section('breadcrumb', __('Balance'))

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6 flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">{{ __('Available Balance') }}</p>
            <p class="text-4xl font-bold text-emerald-600 mt-1">{{ money_format($affiliate->balance) }}</p>
        </div>
        <button type="button" onclick="document.getElementById('payout-modal').classList.remove('hidden')"
            class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
            <i class="fa-solid fa-money-check-dollar me-1"></i> {{ __('Request Payout') }}
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="py-3 px-4 text-start">{{ __('Date') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Type') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Amount') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Description') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Running Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-4">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $transaction->type === 'credit' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $transaction->type === 'credit' ? __('Credit') : __('Debit') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ money_format($transaction->amount) }}
                            </td>
                            <td class="py-3 px-4">{{ $transaction->description }}</td>
                            <td class="py-3 px-4 font-medium">{{ money_format($transaction->balance_after) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-400">{{ __('No transactions yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>

    <x-affiliate.payout-modal :affiliate="$affiliate" :min-payout-amount="$minPayoutAmount" />
@endsection
