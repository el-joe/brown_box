<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px; color:#1e293b;">
    <div style="max-width:560px; margin:0 auto; background:#fff; border-radius:12px; padding:32px; border:1px solid #e2e8f0;">
        <h2 style="margin-top:0;">{{ __('Your Invoice') }}</h2>
        <p>{{ __('Hello') }} {{ $order->customer?->name }},</p>
        <p>{{ __('Please find attached the invoice for your order :number.', ['number' => $order->order_number]) }}</p>
        <p style="color:#94a3b8; font-size:12px;">{{ __('Thank you for shopping with :app.', ['app' => config('app.name')]) }}</p>
    </div>
</body>
</html>
