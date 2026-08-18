@extends('admin.layouts.app')

@section('title', __('Affiliate Report'))

@section('breadcrumb')
    <a href="{{ route('admin.reports.index') }}" class="hover:text-slate-700">{{ __('Reports') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Affiliates') }}</span>
@endsection

@section('content')
    @include('admin.reports._filter-bar', ['action' => route('admin.reports.affiliates'), 'from' => $from, 'to' => $to, 'exportType' => 'affiliates'])

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Commissions Earned') }}</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ money_format($earned) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Paid Out') }}</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ money_format($paid) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Pending') }}</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ money_format($pending) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Affiliate') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Code') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Orders') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Total Commission') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Pending') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Approved') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($byAffiliate as $row)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-slate-800 font-medium">
                            <a href="{{ route('admin.affiliates.show', $row->affiliate_id) }}"
                               class="hover:text-blue-600 hover:underline">
                                {{ $row->affiliate?->customer?->name ?? '—' }}
                            </a>
                        </td>
                        <td class="px-3 py-2">
                            <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">
                                {{ $row->affiliate?->code ?? '—' }}
                            </code>
                        </td>
                        <td class="px-3 py-2 text-end text-slate-600">{{ $row->order_count }}</td>
                        <td class="px-3 py-2 text-end font-semibold text-slate-800">
                            {{ money_format((float) $row->total) }}
                        </td>
                        <td class="px-3 py-2 text-end">
                            <span class="text-amber-600 font-medium">{{ money_format((float) $row->pending_amount) }}</span>
                        </td>
                        <td class="px-3 py-2 text-end">
                            <span class="text-emerald-600 font-medium">{{ money_format((float) $row->approved_amount) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-400">{{ __('No data found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
