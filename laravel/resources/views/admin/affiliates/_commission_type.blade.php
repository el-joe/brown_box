@php
    $colors = [
        'fixed_all_orders' => 'bg-amber-100 text-amber-700',
        'per_category' => 'bg-purple-100 text-purple-700',
    ];
    $labels = [
        'fixed_all_orders' => __('Fixed for All Orders'),
        'per_category' => __('Per Category'),
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$affiliate->commission_type] ?? 'bg-slate-100 text-slate-700' }}">
    {{ $labels[$affiliate->commission_type] ?? $affiliate->commission_type }}
</span>
