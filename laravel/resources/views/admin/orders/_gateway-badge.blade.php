@php
    $labels = [
        'bank_transfer' => __('Bank Transfer'),
        'vodafone_cash' => __('Vodafone Cash'),
        'instapay' => __('Instapay'),
        'paymob' => __('Paymob'),
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
    {{ $labels[$order->payment_gateway] ?? ($order->payment_gateway ?? '—') }}
</span>
