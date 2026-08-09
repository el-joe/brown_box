<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px; color:#1e293b;">
    <div style="max-width:560px; margin:0 auto; background:#fff; border-radius:12px; padding:32px; border:1px solid #e2e8f0;">
        @if ($refundRequest->status === \App\Enums\RefundStatus::Processed)
            <h2 style="margin-top:0;">{{ __('Your refund has been processed') }}</h2>
            <p>{{ __('Hello') }} {{ $refundRequest->customer?->name }},</p>
            <p>{{ __('We have processed a refund for your order. The amount will be credited according to your original payment method.') }}</p>
        @else
            <h2 style="margin-top:0;">{{ __('Your refund request was not approved') }}</h2>
            <p>{{ __('Hello') }} {{ $refundRequest->customer?->name }},</p>
            <p>{{ __('Unfortunately, we were unable to approve your refund request.') }}</p>
        @endif

        <table style="width:100%; border-collapse:collapse; margin:20px 0;">
            <tr>
                <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
                <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $refundRequest->order->order_number }}</td>
            </tr>
            @if ($refundRequest->status === \App\Enums\RefundStatus::Processed)
                <tr>
                    <td style="padding:6px 0; color:#64748b;">{{ __('Refund Amount') }}</td>
                    <td style="padding:6px 0; text-align:right;">{{ money_format((float) $refundRequest->refund_amount) }}</td>
                </tr>
            @endif
            @if ($refundRequest->admin_notes)
                <tr>
                    <td style="padding:6px 0; color:#64748b;">{{ __('Notes') }}</td>
                    <td style="padding:6px 0; text-align:right;">{{ $refundRequest->admin_notes }}</td>
                </tr>
            @endif
        </table>

        <p style="color:#94a3b8; font-size:12px;">{{ __('Thank you for shopping with :app.', ['app' => config('app.name')]) }}</p>
    </div>
</body>
</html>
