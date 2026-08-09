@props(['name', 'current' => null, 'preview' => true])

<div class="admin-field" x-data="{ previewUrl: @js($current ? asset_url($current) : null) }">
    <label class="block cursor-pointer">
        <div class="w-32 h-32 rounded-lg border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden bg-slate-50 hover:border-amber-500 transition-colors">
            @if ($preview)
                <template x-if="previewUrl">
                    <img :src="previewUrl" class="w-full h-full object-cover">
                </template>
                <template x-if="!previewUrl">
                    <i class="fa-solid fa-image text-slate-300 text-2xl"></i>
                </template>
            @else
                <i class="fa-solid fa-image text-slate-300 text-2xl"></i>
            @endif
        </div>

        <input
            type="file"
            name="{{ $name }}"
            accept="image/*"
            class="hidden"
            @change="previewUrl = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : previewUrl"
            {{ $attributes }}
        >
    </label>
</div>
