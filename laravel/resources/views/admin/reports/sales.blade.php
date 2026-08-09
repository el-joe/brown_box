@extends('admin.layouts.app')

@section('title', __('Sales Report'))

@section('breadcrumb')
    <a href="{{ route('admin.reports.index') }}" class="hover:text-slate-700">{{ __('Reports') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Sales') }}</span>
@endsection

@section('content')
    @include('admin.reports._filter-bar', ['action' => route('admin.reports.sales'), 'from' => $from, 'to' => $to, 'exportType' => 'sales', 'groupBy' => true])

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Total Orders') }}</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $totalOrders }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Revenue') }}</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ money_format($revenue) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Avg Order Value') }}</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ money_format($avgOrderValue) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Period') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Orders') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Revenue') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($chart as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 text-slate-600">{{ $row->period }}</td>
                        <td class="px-3 py-2 text-end text-slate-600">{{ $row->orders }}</td>
                        <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $row->revenue) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-3 py-8 text-center text-slate-400">{{ __('No data found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
