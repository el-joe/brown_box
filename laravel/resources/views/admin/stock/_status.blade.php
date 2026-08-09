@php
    $state = $stock->qty <= 0 ? 'out' : ($stock->qty <= $stock->min_qty_alert ? 'low' : 'ok');
    $colors = [
        'ok' => 'bg-emerald-100 text-emerald-700',
        'low' => 'bg-amber-100 text-amber-700',
        'out' => 'bg-red-100 text-red-700',
    ];
    $labels = [
        'ok' => __('OK'),
        'low' => __('Low Stock'),
        'out' => __('Out of Stock'),
    ];
@endphp

<span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$state] }}">{{ $labels[$state] }}</span>
