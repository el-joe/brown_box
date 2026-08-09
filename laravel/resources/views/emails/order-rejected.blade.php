@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Your order has been rejected') }}</h2>
    <p>{{ __('Hello') }} {{ $order->customer?->name }},</p>
    <p>{{ __('Unfortunately, we were unable to process your order. Please contact support for more information.') }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
    </table>
@endsection
