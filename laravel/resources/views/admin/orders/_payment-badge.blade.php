@php
    $colors = [
        'unpaid' => 'bg-slate-100 text-slate-700',
        'pending_verification' => 'bg-amber-100 text-amber-700',
        'paid' => 'bg-emerald-100 text-emerald-700',
        'failed' => 'bg-red-100 text-red-700',
        'refunded' => 'bg-purple-100 text-purple-700',
    ];
    $labels = [
        'unpaid' => __('Unpaid'),
        'pending_verification' => __('Pending Verification'),
        'paid' => __('Paid'),
        'failed' => __('Failed'),
        'refunded' => __('Refunded'),
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$order->payment_status] ?? 'bg-slate-100 text-slate-700' }}">
    {{ $labels[$order->payment_status] ?? $order->payment_status }}
</span>
