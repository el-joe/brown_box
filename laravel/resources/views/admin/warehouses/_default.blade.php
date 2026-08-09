@if ($warehouse->is_default)
    <span class="px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
        <i class="fa-solid fa-star me-1"></i>{{ __('Default') }}
    </span>
@else
    <button type="button"
        onclick="fetch(this.dataset.url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(() => $('#warehouses-table').DataTable().ajax.reload())"
        data-url="{{ route('admin.warehouses.make-default', $warehouse) }}"
        class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 hover:bg-slate-200">
        {{ __('Set Default') }}
    </button>
@endif
