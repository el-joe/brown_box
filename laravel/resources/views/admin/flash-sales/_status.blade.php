@php
    $now = now();
    $status = ! $flashSale->is_active
        ? 'inactive'
        : ($flashSale->starts_at->isFuture() ? 'upcoming' : ($flashSale->ends_at->isPast() ? 'ended' : 'active'));

    $colors = [
        'active' => 'bg-emerald-100 text-emerald-700',
        'upcoming' => 'bg-blue-100 text-blue-700',
        'ended' => 'bg-slate-100 text-slate-500',
        'inactive' => 'bg-slate-100 text-slate-400',
    ][$status];

    $labels = [
        'active' => __('Active'),
        'upcoming' => __('Upcoming'),
        'ended' => __('Ended'),
        'inactive' => __('Inactive'),
    ][$status];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors }}">
    {{ $labels }}
</span>
