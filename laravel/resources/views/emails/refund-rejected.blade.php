@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Your refund request was not approved') }}</h2>
    <p>{{ __('Hello') }} {{ $order->customer?->name }},</p>
    <p>{{ __('Unfortunately, we were unable to approve your refund request.') }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        @if ($reason)
            <tr>
                <td style="padding:6px 0; color:#64748b;">{{ __('Reason') }}</td>
                <td style="padding:6px 0; text-align:right;">{{ $reason }}</td>
            </tr>
        @endif
    </table>
@endsection
