<div class="flex items-center gap-3">
    <a href="{{ route('admin.purchases.show', $purchase) }}" class="text-slate-400 hover:text-amber-600" title="{{ __('View') }}">
        <i class="fa-solid fa-eye"></i>
    </a>

    @if ($purchase->status === 'draft')
        <form action="{{ route('admin.purchases.confirm', $purchase) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Confirm this purchase? Stock will be updated.') }}')">
            @csrf
            <button type="submit" class="text-slate-400 hover:text-emerald-600" title="{{ __('Confirm') }}">
                <i class="fa-solid fa-circle-check"></i>
            </button>
        </form>

        <button type="button" class="text-slate-400 hover:text-red-600" title="{{ __('Delete') }}"
            onclick="confirmAdminDelete('{{ route('admin.purchases.destroy', $purchase) }}')">
            <i class="fa-solid fa-trash"></i>
        </button>
    @endif
</div>
