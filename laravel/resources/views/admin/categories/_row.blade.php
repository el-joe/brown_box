@php $depth = $depth ?? 0; @endphp

@foreach ($nodes as $node)
    <tr data-id="{{ $node->id }}" data-parent-id="{{ $node->parent_id }}" data-depth="{{ $depth }}" class="border-b border-slate-100 hover:bg-slate-50">
        <td class="px-3 py-2 w-8 text-slate-300 cursor-move drag-handle"><i class="fa-solid fa-grip-vertical"></i></td>
        <td class="px-3 py-2 w-14">
            @if ($node->image)
                <img src="{{ asset_url($node->image) }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200">
            @else
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300">
                    <i class="fa-solid fa-image"></i>
                </div>
            @endif
        </td>
        <td class="px-3 py-2 w-10 text-center">
            @if ($node->icon)
                <i class="fa-solid {{ $node->icon }} text-slate-500"></i>
            @endif
        </td>
        <td class="px-3 py-2">
            <div style="padding-inline-start: {{ $depth * 24 }}px" class="flex items-center gap-2">
                @if ($node->childrenTree->isNotEmpty())
                    <button type="button" class="toggle-children text-slate-400 hover:text-slate-700" data-id="{{ $node->id }}">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                @else
                    <span class="w-3 inline-block"></span>
                @endif
                <div>
                    <div class="text-sm font-medium text-slate-800">{{ $node->getTranslation('name', 'en') }}</div>
                    <div class="text-xs text-slate-400" dir="rtl">{{ $node->getTranslation('name', 'ar') }}</div>
                </div>
            </div>
        </td>
        <td class="px-3 py-2 text-sm text-slate-500">{{ $node->parent?->name ?? '—' }}</td>
        <td class="px-3 py-2">
            <button type="button"
                x-data="{ active: {{ $node->is_active ? 'true' : 'false' }} }"
                @click="fetch(@js(route('admin.categories.toggle-active', $node)), { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(r => r.json()).then(d => active = d.is_active)"
                class="px-2 py-1 rounded-full text-xs font-medium"
                :class="active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                <span x-text="active ? @js(__('Active')) : @js(__('Inactive'))"></span>
            </button>
        </td>
        <td class="px-3 py-2 text-sm text-slate-500">{{ $node->sort_order }}</td>
        <td class="px-3 py-2 text-end whitespace-nowrap">
            <a href="{{ route('admin.categories.edit', $node) }}" class="text-slate-400 hover:text-amber-600 me-2">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button type="button" @click="confirmAdminDelete(@js(route('admin.categories.destroy', $node)))" class="text-slate-400 hover:text-red-600">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>

    @if ($node->childrenTree->isNotEmpty())
        @include('admin.categories._row', ['nodes' => $node->childrenTree, 'depth' => $depth + 1])
    @endif
@endforeach
