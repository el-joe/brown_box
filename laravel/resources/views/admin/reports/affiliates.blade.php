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
                    <th class="px-3 py-2 text-end">{{ __('Commissions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($byAffiliate as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 text-slate-600">{{ $row->affiliate?->customer?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $row->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-3 py-8 text-center text-slate-400">{{ __('No data found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
