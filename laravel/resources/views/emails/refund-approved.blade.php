@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Your refund has been approved') }}</h2>
    <p>{{ __('Hello') }} {{ $order->customer?->name }},</p>
    <p>{{ __('Your refund request has been approved. The amount will be credited according to your original payment method.') }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Refund Amount') }}</td>
            <td style="padding:6px 0; text-align:right;">{{ money_format((float) $amount) }}</td>
        </tr>
    </table>
@endsection
