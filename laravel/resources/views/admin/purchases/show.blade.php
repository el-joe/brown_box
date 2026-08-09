@extends('admin.layouts.app')

@section('title', __('Purchase :invoice', ['invoice' => $purchase->invoice_number]))

@section('breadcrumb')
    <a href="{{ route('admin.purchases.index') }}" class="hover:text-slate-700">{{ __('Purchases') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $purchase->invoice_number }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6 print:hidden">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Purchase :invoice', ['invoice' => $purchase->invoice_number]) }}</h1>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-print me-1"></i>{{ __('Print') }}
            </button>
            @if ($purchase->status === 'draft')
                <form action="{{ route('admin.purchases.confirm', $purchase) }}" method="POST" onsubmit="return confirm('{{ __('Confirm this purchase? Stock will be updated.') }}')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                        <i class="fa-solid fa-circle-check me-1"></i>{{ __('Confirm Purchase') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 print:shadow-none print:border-0">
        <div class="flex items-start justify-between mb-6 pb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Brown Box</h2>
                <p class="text-xs text-slate-500 mt-1">{{ __('Purchase Invoice') }}</p>
            </div>
            <div class="text-end">
                <p class="text-sm font-semibold text-slate-700">{{ $purchase->invoice_number }}</p>
                <p class="text-xs text-slate-500">{{ $purchase->created_at?->format('Y-m-d H:i') }}</p>
                @include('admin.purchases._status', ['purchase' => $purchase])
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase mb-1">{{ __('Supplier') }}</p>
                <p class="text-sm text-slate-700">{{ $purchase->supplier?->name ?? '—' }}</p>
                <p class="text-xs text-slate-500">{{ $purchase->supplier?->phone }}</p>
                <p class="text-xs text-slate-500">{{ $purchase->supplier?->email }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase mb-1">{{ __('Warehouse') }}</p>
                <p class="text-sm text-slate-700">{{ $purchase->warehouse?->name ?? '—' }}</p>
                <p class="text-xs text-slate-500">{{ $purchase->warehouse?->address }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase mb-1">{{ __('Recorded By') }}</p>
                <p class="text-sm text-slate-700">{{ $purchase->admin?->name ?? '—' }}</p>
            </div>
        </div>

        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-3 py-2 text-start">{{ __('Product') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('SKU') }}</th>
                        <th class="px-3 py-2 text-end">{{ __('Qty') }}</th>
                        <th class="px-3 py-2 text-end">{{ __('Unit Cost') }}</th>
                        <th class="px-3 py-2 text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2">{{ $item->product?->getTranslation('name', app()->getLocale()) ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $item->variant?->sku ?? $item->product?->sku ?? '—' }}</td>
                            <td class="px-3 py-2 text-end">{{ (int) $item->qty }}</td>
                            <td class="px-3 py-2 text-end">{{ number_format((float) $item->unit_cost, 2) }}</td>
                            <td class="px-3 py-2 text-end font-medium">{{ number_format((float) $item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <div class="w-full md:w-72 space-y-1 text-sm">
                <div class="flex justify-between text-slate-500">
                    <span>{{ __('Subtotal') }}</span>
                    <span>{{ number_format((float) $purchase->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>{{ __('Discount') }}</span>
                    <span>- {{ number_format((float) $purchase->discount_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>{{ __('Tax') }}</span>
                    <span>+ {{ number_format((float) $purchase->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-semibold text-slate-800 pt-2 border-t border-slate-100">
                    <span>{{ __('Grand Total') }}</span>
                    <span>{{ number_format((float) $purchase->net_amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if ($purchase->notes)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs font-medium text-slate-400 uppercase mb-1">{{ __('Notes') }}</p>
                <p class="text-sm text-slate-600 whitespace-pre-line">{{ $purchase->notes }}</p>
            </div>
        @endif

        @if ($purchase->status === 'draft')
            <p class="mt-6 text-xs text-amber-600 print:hidden">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                {{ __('This purchase is a draft and has not affected stock or accounting yet.') }}
            </p>
        @endif
    </div>
@endsection

@push('styles')
    <style media="print">
        aside, header, .print\:hidden { display: none !important; }
        main { padding: 0 !important; }
        body { background: #fff !important; }
    </style>
@endpush
