@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Your order has been confirmed') }}</h2>
    <p>{{ __('Hello') }} {{ $order->customer?->name }},</p>
    <p>{{ __('Great news! Your order has been confirmed and is now being prepared.') }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Total') }}</td>
            <td style="padding:6px 0; text-align:right;">{{ money_format((float) $order->total_amount) }}</td>
        </tr>
    </table>
@endsection
