@php
    $colors = [
        'pending' => 'bg-slate-100 text-slate-700',
        'approved' => 'bg-blue-100 text-blue-700',
        'paid' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-red-100 text-red-700',
    ];
    $labels = [
        'pending' => __('Pending'),
        'approved' => __('Approved'),
        'paid' => __('Paid'),
        'rejected' => __('Rejected'),
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$payout->status] ?? 'bg-slate-100 text-slate-700' }}">
    {{ $labels[$payout->status] ?? $payout->status }}
</span>
