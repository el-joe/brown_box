<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 20px; margin-bottom: 0; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        .text-right { text-align: right; }
        .totals td { border: none; padding: 4px 8px; }
        .totals .label { color: #64748b; }
        .header { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <table style="border:none; margin:0;">
        <tr>
            <td style="border:none;">
                <h1>{{ config('app.name') }}</h1>
                <div class="muted">{{ __('Invoice') }}</div>
            </td>
            <td style="border:none;" class="text-right">
                <div><strong>{{ __('Order') }}:</strong> {{ $order->order_number }}</div>
                <div><strong>{{ __('Date') }}:</strong> {{ $order->created_at->format('Y-m-d') }}</div>
            </td>
        </tr>
    </table>

    <table style="border:none;">
        <tr>
            <td style="border:none; width:50%;">
                <strong>{{ __('Bill To') }}</strong><br>
                {{ $order->customer?->name }}<br>
                {{ $order->customer?->email }}<br>
                {{ $order->customer?->phone }}
            </td>
            <td style="border:none;">
                <strong>{{ __('Shipping Address') }}</strong><br>
                {{ $order->customer_address['address_line'] ?? '' }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>{{ __('Product') }}</th>
                <th>{{ __('Variant') }}</th>
                <th class="text-right">{{ __('Qty') }}</th>
                <th class="text-right">{{ __('Unit Price') }}</th>
                <th class="text-right">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->variant_label ?? '—' }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">{{ money_format((float) $item->unit_price) }}</td>
                    <td class="text-right">{{ money_format((float) $item->total_price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals" style="width:280px; margin-left:auto;">
        <tr>
            <td class="label">{{ __('Subtotal') }}</td>
            <td class="text-right">{{ money_format((float) $order->subtotal) }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Coupon Discount') }}</td>
            <td class="text-right">-{{ money_format((float) $order->coupon_discount) }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Shipping') }}</td>
            <td class="text-right">{{ money_format((float) $order->shipping_amount) }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Tax') }}</td>
            <td class="text-right">{{ money_format((float) $order->tax_amount) }}</td>
        </tr>
        <tr>
            <td class="label"><strong>{{ __('Total') }}</strong></td>
            <td class="text-right"><strong>{{ money_format((float) $order->total_amount) }}</strong></td>
        </tr>
    </table>
</body>
</html>
