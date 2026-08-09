@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'text-sm text-slate-500 flex items-center flex-wrap gap-2']) }}>
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <i class="fa-solid fa-chevron-{{ current_lang() === 'ar' ? 'left' : 'right' }} text-xs text-slate-300"></i>
        @endif

        @if (!empty($item['url']))
            <a href="{{ $item['url'] }}" class="hover:text-amber-600">{{ $item['label'] }}</a>
        @else
            <span class="text-slate-700">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
