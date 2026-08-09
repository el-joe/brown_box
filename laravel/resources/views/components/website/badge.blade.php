@props(['text', 'color' => 'amber'])

@php
    $colors = [
        'amber' => 'bg-amber-100 text-amber-700',
        'red' => 'bg-red-100 text-red-700',
        'green' => 'bg-emerald-100 text-emerald-700',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium '.($colors[$color] ?? $colors['amber'])]) }}>
    {{ $text }}
</span>
