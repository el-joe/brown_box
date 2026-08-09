@extends('admin.layouts.app')

@section('title', __('Stock Report'))

@section('breadcrumb')
    <a href="{{ route('admin.reports.index') }}" class="hover:text-slate-700">{{ __('Reports') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Stock') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Total Stock Valuation') }}</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ money_format($valuation) }}</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.export', ['type' => 'stock', 'format' => 'excel']) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-file-excel me-1"></i>{{ __('Excel') }}
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'stock', 'format' => 'pdf']) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-file-pdf me-1"></i>{{ __('PDF') }}
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Product') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Warehouse') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Qty') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Valuation') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stocks as $stock)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 text-slate-600">{{ $stock->product?->getTranslation('name', app()->getLocale()) ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $stock->warehouse?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-end text-slate-600">{{ $stock->qty }}</td>
                        <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) ($stock->product?->cost_price ?? 0) * $stock->qty) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-8 text-center text-slate-400">{{ __('No data found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
