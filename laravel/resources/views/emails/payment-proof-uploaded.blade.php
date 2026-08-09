@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Payment Proof Uploaded') }}</h2>
    <p>{{ __('Customer :customer uploaded payment proof for order :number.', [
        'customer' => $order->customer?->name ?? __('Guest'),
        'number' => $order->order_number,
    ]) }}</p>

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

    <p><a href="{{ route('admin.orders.show', $order->id) }}" style="color:#1e293b; font-weight:bold;">{{ __('Review Order') }}</a></p>
@endsection
