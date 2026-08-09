@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Your order is on its way') }}</h2>
    <p>{{ __('Hello') }} {{ $order->customer?->name }},</p>
    <p>{{ __('Your order has been shipped.') }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        @if ($order->tracking_number)
            <tr>
                <td style="padding:6px 0; color:#64748b;">{{ __('Tracking Number') }}</td>
                <td style="padding:6px 0; text-align:right;">{{ $order->tracking_number }}</td>
            </tr>
        @endif
        @if ($order->shippingCompany)
            <tr>
                <td style="padding:6px 0; color:#64748b;">{{ __('Shipping Company') }}</td>
                <td style="padding:6px 0; text-align:right;">{{ $order->shippingCompany->name }}</td>
            </tr>
        @endif
    </table>
@endsection
