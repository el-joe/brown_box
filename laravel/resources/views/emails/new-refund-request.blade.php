@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('New Refund Request') }}</h2>
    <p>{{ __('Customer :customer requested a refund of :amount for order :number.', [
        'customer' => $refundRequest->customer?->name ?? __('Unknown'),
        'amount' => money_format((float) ($refundRequest->refund_amount ?? 0)),
        'number' => $refundRequest->order?->order_number,
    ]) }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $refundRequest->order?->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Reason') }}</td>
            <td style="padding:6px 0; text-align:right;">{{ $refundRequest->reason }}</td>
        </tr>
    </table>

    <p><a href="{{ route('admin.refunds.show', $refundRequest->id) }}" style="color:#1e293b; font-weight:bold;">{{ __('Review Refund Request') }}</a></p>
@endsection
