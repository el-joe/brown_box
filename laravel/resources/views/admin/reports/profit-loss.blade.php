@extends('admin.layouts.app')

@section('title', __('Profit & Loss'))

@section('breadcrumb')
    <a href="{{ route('admin.reports.index') }}" class="hover:text-slate-700">{{ __('Reports') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Profit & Loss') }}</span>
@endsection

@section('content')
    @include('admin.reports._filter-bar', ['action' => route('admin.reports.profit-loss'), 'from' => $from, 'to' => $to, 'exportType' => 'profit-loss'])

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden max-w-xl">
        <table class="w-full text-sm">
            <tbody>
                <tr class="border-b border-slate-100">
                    <td class="px-5 py-3 text-slate-600">{{ __('Revenue') }}</td>
                    <td class="px-5 py-3 text-end font-medium text-emerald-600">{{ money_format($revenue) }}</td>
                </tr>
                <tr class="border-b border-slate-100">
                    <td class="px-5 py-3 text-slate-600">{{ __('COGS (Cost of Goods Sold)') }}</td>
                    <td class="px-5 py-3 text-end font-medium text-red-600">- {{ money_format($cogs) }}</td>
                </tr>
                <tr class="border-b border-slate-100">
                    <td class="px-5 py-3 text-slate-600">{{ __('Expenses') }}</td>
                    <td class="px-5 py-3 text-end font-medium text-red-600">- {{ money_format($expenses) }}</td>
                </tr>
                <tr class="bg-slate-50">
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ __('Net Profit') }}</td>
                    <td class="px-5 py-3 text-end font-bold text-lg {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ money_format($netProfit) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
