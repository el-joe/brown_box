@extends('emails.layout')

@section('content')
    <h2 style="margin-top:0;">{{ __('Your payout has been processed') }}</h2>
    <p>{{ __('Hello') }} {{ $payout->affiliate?->customer?->name }},</p>
    <p>{{ __('Your payout request has been processed.') }}</p>

    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Amount') }}</td>
            <td style="padding:6px 0; text-align:right; font-weight:bold;">{{ money_format((float) $payout->amount) }}</td>
        </tr>
        <tr>
            <td style="padding:6px 0; color:#64748b;">{{ __('Payment Method') }}</td>
            <td style="padding:6px 0; text-align:right;">{{ ucfirst($payout->payment_method ?? '') }}</td>
        </tr>
    </table>
@endsection
