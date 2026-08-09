@php
    $colors = [
        'draft' => 'bg-slate-100 text-slate-700',
        'confirmed' => 'bg-emerald-100 text-emerald-700',
    ];
    $labels = [
        'draft' => __('Draft'),
        'confirmed' => __('Confirmed'),
    ];
@endphp

<span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$purchase->status] ?? 'bg-slate-100 text-slate-700' }}">
    {{ $labels[$purchase->status] ?? ucfirst($purchase->status) }}
</span>
