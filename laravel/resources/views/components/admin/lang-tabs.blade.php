@props(['locales' => ['en', 'ar']])

{{-- Usage: <x-admin.lang-tabs><x-slot:en>...</x-slot:en><x-slot:ar>...</x-slot:ar></x-admin.lang-tabs> --}}
<div x-data="{ activeLangTab: '{{ $locales[0] }}' }" {{ $attributes }}>
    <div class="flex items-center gap-1 border-b border-slate-200 mb-4">
        @foreach ($locales as $locale)
            <button
                type="button"
                @click="activeLangTab = '{{ $locale }}'"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors"
                :class="activeLangTab === '{{ $locale }}' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
            >
                {{ strtoupper($locale) }}
            </button>
        @endforeach
    </div>

    @foreach ($locales as $locale)
        <div x-show="activeLangTab === '{{ $locale }}'" x-cloak>
            {{ ${$locale} ?? '' }}
        </div>
    @endforeach
</div>
