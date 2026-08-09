@extends('admin.layouts.app')

@section('title', __('Product Performance'))

@section('breadcrumb')
    <a href="{{ route('admin.reports.index') }}" class="hover:text-slate-700">{{ __('Reports') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Product Performance') }}</span>
@endsection

@section('content')
    @include('admin.reports._filter-bar', ['action' => route('admin.reports.products'), 'from' => $from, 'to' => $to, 'exportType' => 'products'])

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Product') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Qty Sold') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Revenue') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topProducts as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 text-slate-600">{{ $row->product_name }}</td>
                        <td class="px-3 py-2 text-end text-slate-600">{{ $row->qty_sold }}</td>
                        <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $row->revenue) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-3 py-8 text-center text-slate-400">{{ __('No data found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
