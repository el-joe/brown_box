@php
    $colors = [
        'pending' => 'bg-slate-100 text-slate-700',
        'approved' => 'bg-emerald-100 text-emerald-700',
        'paid' => 'bg-blue-100 text-blue-700',
    ];
    $labels = [
        'pending' => __('Pending'),
        'approved' => __('Approved'),
        'paid' => __('Paid'),
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$commission->status] ?? 'bg-slate-100 text-slate-700' }}">
    {{ $labels[$commission->status] ?? $commission->status }}
</span>
