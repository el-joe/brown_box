<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px; color:#1e293b;">
    <div style="max-width:560px; margin:0 auto; background:#fff; border-radius:12px; padding:32px; border:1px solid #e2e8f0;">
        <h2 style="margin-top:0;">{{ $heading }}</h2>
        <p>{{ __('Hello') }} {{ $order->customer?->name }},</p>
        <p>{{ $message }}</p>

        <table style="width:100%; border-collapse:collapse; margin:20px 0;">
            <tr>
                <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
                <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#64748b;">{{ __('Order Status') }}</td>
                <td style="padding:6px 0; text-align:right;">{{ ucfirst($order->status->value) }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#64748b;">{{ __('Total') }}</td>
                <td style="padding:6px 0; text-align:right;">{{ money_format((float) $order->total_amount) }}</td>
            </tr>
        </table>

        <p style="color:#94a3b8; font-size:12px;">{{ __('Thank you for shopping with :app.', ['app' => config('app.name')]) }}</p>
    </div>
</body>
</html>
