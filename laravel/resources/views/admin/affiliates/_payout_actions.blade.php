@if ($payout->status === 'pending')
    <div x-data="{ rejecting: false }" class="flex items-center gap-2">
        <form method="POST" action="{{ route('admin.affiliates.payouts.mark-paid', $payout) }}">
            @csrf
            <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                <i class="fa-solid fa-check me-1"></i>{{ __('Mark as Paid') }}
            </button>
        </form>
        <button type="button" @click="rejecting = true" class="text-red-600 hover:text-red-800 text-xs font-medium">
            <i class="fa-solid fa-xmark me-1"></i>{{ __('Reject') }}
        </button>

        <div x-show="rejecting" x-cloak @click.outside="rejecting = false" class="absolute z-10 mt-2 bg-white border border-slate-200 rounded-lg shadow-lg p-3 w-64">
            <form method="POST" action="{{ route('admin.affiliates.payouts.reject', $payout) }}">
                @csrf
                <textarea name="notes" rows="2" placeholder="{{ __('Rejection notes (optional)') }}" class="w-full rounded-lg border-slate-300 text-xs mb-2"></textarea>
                <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700">
                    {{ __('Confirm Reject') }}
                </button>
            </form>
        </div>
    </div>
@else
    <span class="text-slate-300">—</span>
@endif
