@props(['model' => null])

<div x-data="{ expanded: true }" {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-sm']) }}>
    <button
        type="button"
        @click="expanded = !expanded"
        class="w-full flex items-center justify-between px-5 py-4 border-b border-slate-200"
    >
        <span class="text-sm font-semibold text-slate-800">
            <i class="fa-solid fa-filter me-2 text-slate-400"></i>{{ __('Filters') }}
            @if ($model)
                <span class="text-xs text-slate-400 font-normal">({{ $model }})</span>
            @endif
        </span>
        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="expanded" x-collapse>
        <div class="p-5 space-y-4">
            {{ $slot }}
        </div>
    </div>
</div>
