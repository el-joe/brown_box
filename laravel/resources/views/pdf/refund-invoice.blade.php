<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" @if(($locale ?? 'en') === 'ar') dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4; margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 20px; margin-bottom: 0; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        .text-right { text-align: right; }
        .totals td { border: none; padding: 4px 8px; }
        .totals .label { color: #64748b; }
        .print-btn { margin-bottom: 16px; padding: 8px 16px; background: #1e293b; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    @php $order = $refundRequest->order; @endphp

    @if (empty($isPdf))
        <button class="print-btn" onclick="window.print()">{{ __('Print') }}</button>
    @endif

    <table style="border:none; margin:0;">
        <tr>
            <td style="border:none;">
                <h1>{{ config('app.name') }}</h1>
                <div class="muted">{{ __('Refund Invoice') }}</div>
            </td>
            <td style="border:none;" class="text-right">
                <div><strong>{{ __('Refund Request') }}:</strong> #{{ $refundRequest->id }}</div>
                <div><strong>{{ __('Order') }}:</strong> {{ $order?->order_number }}</div>
                <div><strong>{{ __('Date') }}:</strong> {{ $refundRequest->created_at->format('Y-m-d') }}</div>
            </td>
        </tr>
    </table>

    <table style="border:none;">
        <tr>
            <td style="border:none; width:50%;">
                <strong>{{ __('Customer') }}</strong><br>
                {{ $refundRequest->customer?->name }}<br>
                {{ $refundRequest->customer?->email }}
            </td>
            <td style="border:none;">
                <strong>{{ __('Status') }}</strong><br>
                {{ ucfirst($refundRequest->status->value ?? $refundRequest->status) }}
            </td>
        </tr>
    </table>

    @if ($order)
        <table>
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Variant') }}</th>
                    <th class="text-right">{{ __('Qty') }}</th>
                    <th class="text-right">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->variant_label ?? '—' }}</td>
                        <td class="text-right">{{ $item->qty }}</td>
                        <td class="text-right">{{ money_format((float) $item->total_price) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table style="border:none; margin-top:16px;">
        <tr>
            <td style="border:none;">
                <strong>{{ __('Reason') }}</strong><br>
                {{ $refundRequest->reason }}
                @if ($refundRequest->details)
                    <br><span class="muted">{{ $refundRequest->details }}</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="totals" style="width:280px; margin-left:auto;">
        <tr>
            <td class="label"><strong>{{ __('Refund Amount') }}</strong></td>
            <td class="text-right"><strong>{{ money_format((float) $refundRequest->refund_amount) }}</strong></td>
        </tr>
    </table>
</body>
</html>
