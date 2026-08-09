@props(['value' => 0, 'count' => 0])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1']) }}>
    @for ($i = 1; $i <= 5; $i++)
        <i class="fa-{{ $i <= round($value) ? 'solid' : 'regular' }} fa-star text-amber-400 text-xs"></i>
    @endfor
    @if ($count > 0)
        <span class="text-xs text-slate-400 ms-1">({{ $count }})</span>
    @endif
</div>
