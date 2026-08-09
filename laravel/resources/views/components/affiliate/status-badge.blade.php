@props(['status'])

@php
    $classes = match ($status) {
        'pending' => 'bg-amber-100 text-amber-700',
        'available' => 'bg-blue-100 text-blue-700',
        'approved' => 'bg-blue-100 text-blue-700',
        'paid' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-red-100 text-red-700',
        default => 'bg-slate-100 text-slate-600',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium $classes"]) }}>
    {{ __(ucfirst($status)) }}
</span>
