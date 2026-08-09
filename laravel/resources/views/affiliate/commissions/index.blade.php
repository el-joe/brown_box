@extends('affiliate.layouts.app')

@section('title', __('Commissions'))
@section('breadcrumb', __('Commissions'))

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500">{{ __('Total Earned') }}</p>
            <p class="text-xl font-bold text-slate-800">{{ money_format($summary['total_earned']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500">{{ __('Pending') }}</p>
            <p class="text-xl font-bold text-amber-600">{{ money_format($summary['pending']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500">{{ __('Available') }}</p>
            <p class="text-xl font-bold text-blue-600">{{ money_format($summary['available']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500">{{ __('Paid') }}</p>
            <p class="text-xl font-bold text-emerald-600">{{ money_format($summary['paid']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex items-end gap-3">
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('Status') }}</label>
                <select name="status" class="rounded-lg border border-slate-300 text-sm py-2 px-3">
                    <option value="">{{ __('All') }}</option>
                    <option value="pending" @selected(request('status') === 'pending')>{{ __('Pending') }}</option>
                    <option value="approved" @selected(request('status') === 'approved')>{{ __('Approved') }}</option>
                    <option value="paid" @selected(request('status') === 'paid')>{{ __('Paid') }}</option>
                </select>
            </div>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="py-3 px-4 text-start">{{ __('Order #') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Order Total') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Rate') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Commission') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Status') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Delivered At') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Available At') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Paid At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($commissions as $commission)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-4">{{ $commission->order?->order_number ?? __('Manual') }}</td>
                            <td class="py-3 px-4">{{ $commission->order ? money_format($commission->order->total_amount) : '—' }}</td>
                            <td class="py-3 px-4">
                                {{ $commission->order && (float) $commission->order->subtotal > 0
                                    ? number_format(($commission->amount / $commission->order->subtotal) * 100, 2).'%'
                                    : '—' }}
                            </td>
                            <td class="py-3 px-4 font-medium">{{ money_format($commission->amount) }}</td>
                            <td class="py-3 px-4"><x-affiliate.status-badge :status="$commission->display_status" /></td>
                            <td class="py-3 px-4">{{ $commission->order?->delivered_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="py-3 px-4">{{ $commission->available_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="py-3 px-4">{{ $commission->paid_at?->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-slate-400">{{ __('No commissions found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $commissions->links() }}</div>
@endsection
