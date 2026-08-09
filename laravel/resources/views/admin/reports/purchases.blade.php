@extends('admin.layouts.app')

@section('title', __('Purchases Report'))

@section('breadcrumb')
    <a href="{{ route('admin.reports.index') }}" class="hover:text-slate-700">{{ __('Reports') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Purchases') }}</span>
@endsection

@section('content')
    @include('admin.reports._filter-bar', ['action' => route('admin.reports.purchases'), 'from' => $from, 'to' => $to, 'exportType' => 'purchases'])

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Supplier') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Purchases') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bySupplier as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 text-slate-600">{{ $row->supplier?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-end text-slate-600">{{ $row->purchases_count }}</td>
                        <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $row->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-3 py-8 text-center text-slate-400">{{ __('No data found.') }}</td></tr>
                @endforelse
            </tbody>
            @if ($bySupplier->isNotEmpty())
                <tfoot class="bg-slate-50">
                    <tr>
                        <td class="px-3 py-2 text-end font-semibold text-slate-700" colspan="2">{{ __('Total') }}</td>
                        <td class="px-3 py-2 text-end font-bold text-slate-900">{{ money_format($total) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endsection
