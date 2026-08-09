@php
    $colors = [
        'pending' => 'bg-slate-100 text-slate-700',
        'confirmed' => 'bg-blue-100 text-blue-700',
        'processing' => 'bg-indigo-100 text-indigo-700',
        'shipped' => 'bg-purple-100 text-purple-700',
        'delivered' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'refunded' => 'bg-amber-100 text-amber-700',
    ];
    $labels = [
        'pending' => __('Pending'),
        'confirmed' => __('Confirmed'),
        'processing' => __('Processing'),
        'shipped' => __('Shipped'),
        'delivered' => __('Delivered'),
        'cancelled' => __('Cancelled'),
        'refunded' => __('Refunded'),
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
    {{ $labels[$order->status] ?? $order->status }}
</span>
