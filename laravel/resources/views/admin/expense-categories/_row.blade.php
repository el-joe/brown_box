@php $depth = $depth ?? 0; @endphp

@foreach ($nodes as $node)
    <tr class="border-b border-slate-100 hover:bg-slate-50">
        <td class="px-3 py-2">
            <div style="padding-inline-start: {{ $depth * 24 }}px">
                <div class="text-sm font-medium text-slate-800">{{ $node->getTranslation('name', 'en') }}</div>
                <div class="text-xs text-slate-400" dir="rtl">{{ $node->getTranslation('name', 'ar') }}</div>
            </div>
        </td>
        <td class="px-3 py-2">
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $node->type === 'debit' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ $node->type === 'debit' ? __('Debit') : __('Credit') }}
            </span>
        </td>
        <td class="px-3 py-2">
            <button type="button"
                x-data="{ active: {{ $node->is_active ? 'true' : 'false' }} }"
                @click="fetch(@js(route('admin.expense-categories.toggle-active', $node)), { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(r => r.json()).then(d => active = d.is_active)"
                class="px-2 py-1 rounded-full text-xs font-medium"
                :class="active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                <span x-text="active ? @js(__('Active')) : @js(__('Inactive'))"></span>
            </button>
        </td>
        <td class="px-3 py-2 text-end whitespace-nowrap">
            <a href="{{ route('admin.expense-categories.edit', $node) }}" class="text-slate-400 hover:text-amber-600 me-2">
                <i class="fa-solid fa-pen"></i>
            </a>
            <button type="button" @click="confirmAdminDelete(@js(route('admin.expense-categories.destroy', $node)))" class="text-slate-400 hover:text-red-600">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>

    @if ($node->childrenTree->isNotEmpty())
        @include('admin.expense-categories._row', ['nodes' => $node->childrenTree, 'depth' => $depth + 1])
    @endif
@endforeach
