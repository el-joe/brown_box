@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>
    @if ($subtitle)
        <p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>
    @endif
</div>
