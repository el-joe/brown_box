@php
    $colors = [
        'purchase' => 'bg-emerald-100 text-emerald-700',
        'sale' => 'bg-blue-100 text-blue-700',
        'adjustment' => 'bg-amber-100 text-amber-700',
        'return' => 'bg-purple-100 text-purple-700',
        'transfer' => 'bg-slate-100 text-slate-700',
        'in' => 'bg-emerald-100 text-emerald-700',
        'out' => 'bg-red-100 text-red-700',
    ];
    $color = $colors[$movement->type] ?? 'bg-slate-100 text-slate-700';
@endphp

<span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">{{ ucfirst($movement->type) }}</span>
