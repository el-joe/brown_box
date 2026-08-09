@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Your commission has been approved') }}</h2>
    <p>{{ __('Hello') }} {{ $commission->affiliate?->customer?->name }},</p>
    <p>{{ __('A commission has been approved on your affiliate account.') }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Order Number') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ $commission->order?->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Commission Amount') }}</td>
            <td style="padding:6px 0; text-align:right;">{{ money_format((float) $commission->amount) }}</td>
        </tr>
    </table>
@endsection
