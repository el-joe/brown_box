@php
    $colors = match ($coupon->type) {
        'free_shipping' => 'bg-blue-100 text-blue-700',
        'percentage' => 'bg-purple-100 text-purple-700',
        'fixed' => 'bg-amber-100 text-amber-700',
        default => 'bg-slate-100 text-slate-700',
    };

    $labels = [
        'free_shipping' => __('Free Shipping'),
        'percentage' => __('Percentage'),
        'fixed' => __('Fixed Amount'),
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors }}">
    {{ $labels[$coupon->type] ?? $coupon->type }}
</span>
