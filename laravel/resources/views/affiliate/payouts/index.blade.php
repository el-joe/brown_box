@extends('affiliate.layouts.app')

@section('title', __('Payouts'))
@section('breadcrumb', __('Payouts'))

@section('content')
    <div class="flex justify-end mb-4">
        <button type="button" onclick="document.getElementById('payout-modal').classList.remove('hidden')"
            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fa-solid fa-plus me-1"></i> {{ __('New Payout Request') }}
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="py-3 px-4 text-start">{{ __('Date') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Amount') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Method') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Status') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Processed At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payouts as $payout)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-4">{{ $payout->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 font-medium">{{ money_format($payout->amount) }}</td>
                            <td class="py-3 px-4">{{ __(ucfirst($payout->payment_method)) }}</td>
                            <td class="py-3 px-4"><x-affiliate.status-badge :status="$payout->status" /></td>
                            <td class="py-3 px-4">{{ $payout->processed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-400">{{ __('No payout requests yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $payouts->links() }}</div>

    <x-affiliate.payout-modal :affiliate="$affiliate" :min-payout-amount="$minPayoutAmount" />
@endsection
