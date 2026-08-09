@extends('affiliate.layouts.app')

@section('title', __('Orders'))
@section('breadcrumb', __('Orders'))

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('Commission Status') }}</label>
                <select name="status" class="rounded-lg border border-slate-300 text-sm py-2 px-3">
                    <option value="">{{ __('All') }}</option>
                    <option value="pending" @selected(request('status') === 'pending')>{{ __('Pending') }}</option>
                    <option value="available" @selected(request('status') === 'available')>{{ __('Available') }}</option>
                    <option value="paid" @selected(request('status') === 'paid')>{{ __('Paid') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('From') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-slate-300 text-sm py-2 px-3">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('To') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-slate-300 text-sm py-2 px-3">
            </div>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                {{ __('Filter') }}
            </button>
            <a href="{{ route('affiliate.orders.index') }}" class="text-sm text-slate-500 hover:text-slate-700">{{ __('Reset') }}</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="py-3 px-4 text-start">{{ __('Order #') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Date') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Customer') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Total') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Commission') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Status') }}</th>
                        <th class="py-3 px-4 text-start">{{ __('Available At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php $commission = $order->affiliateCommissions->first(); @endphp
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-4">{{ $order->order_number }}</td>
                            <td class="py-3 px-4">{{ $order->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4">{{ __('Customer #:id', ['id' => $order->customer_id]) }}</td>
                            <td class="py-3 px-4">{{ money_format($order->total_amount) }}</td>
                            <td class="py-3 px-4">{{ $commission ? money_format($commission->amount) : '—' }}</td>
                            <td class="py-3 px-4">
                                @if ($commission)
                                    <x-affiliate.status-badge :status="$commission->display_status" />
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ $commission?->available_at?->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-400">{{ __('No orders found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
