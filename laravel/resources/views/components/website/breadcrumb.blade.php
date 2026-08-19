@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'text-sm text-slate-500 flex items-center flex-wrap gap-2']) }}
    aria-label="{{ __('Breadcrumb') }}"
    itemscope itemtype="https://schema.org/BreadcrumbList">
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <i class="fa-solid fa-chevron-{{ current_lang() === 'ar' ? 'left' : 'right' }} text-xs text-slate-300" aria-hidden="true"></i>
        @endif

        <span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            @if (!empty($item['url']))
                <a href="{{ $item['url'] }}" class="hover:text-amber-600" itemprop="item">
                    <span itemprop="name">{{ $item['label'] }}</span>
                </a>
            @else
                <span class="text-slate-700" itemprop="name">{{ $item['label'] }}</span>
            @endif
            <meta itemprop="position" content="{{ $index + 1 }}">
        </span>
    @endforeach
</nav>
