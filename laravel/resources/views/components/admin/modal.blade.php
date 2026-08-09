@props(['id', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
    $maxWidth = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    @open-modal-{{ $id }}.window="open = true"
    @close-modal-{{ $id }}.window="open = false"
    @keydown.escape.window="open = false"
    id="{{ $id }}"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-slate-900/50"></div>

    <div
        x-show="open"
        x-transition
        @click.stop
        class="relative bg-white rounded-xl shadow-xl w-full {{ $maxWidth }} max-h-[90vh] flex flex-col"
    >
        @isset($header)
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                <div class="text-sm font-semibold text-slate-800">{{ $header }}</div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endisset

        <div class="px-5 py-4 overflow-y-auto">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-slate-200">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
