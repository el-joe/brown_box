@props(['affiliate', 'minPayoutAmount' => 0])

<div id="payout-modal" class="hidden fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4"
    x-data="{ method: 'bank' }">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
            <h3 class="font-semibold text-slate-800">{{ __('Request Payout') }}</h3>
            <button type="button" onclick="document.getElementById('payout-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('affiliate.payouts.store') }}" class="p-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Amount') }}</label>
                <input type="number" step="0.01" name="amount" min="{{ $minPayoutAmount > 0 ? $minPayoutAmount : '0.01' }}"
                    max="{{ (float) $affiliate->balance }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <p class="text-xs text-slate-500 mt-1">
                    {{ __('Available: :balance', ['balance' => money_format($affiliate->balance)]) }}
                    @if ($minPayoutAmount > 0)
                        · {{ __('Minimum: :min', ['min' => money_format($minPayoutAmount)]) }}
                    @endif
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Payment Method') }}</label>
                <select name="payment_method" x-model="method" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="bank">{{ __('Bank Transfer') }}</option>
                    <option value="vodafone">{{ __('Vodafone Cash') }}</option>
                    <option value="instapay">{{ __('InstaPay') }}</option>
                </select>
            </div>

            <div x-show="method === 'bank'" class="space-y-2">
                <input type="text" name="payment_details[account_name]" placeholder="{{ __('Account Holder Name') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <input type="text" name="payment_details[bank_name]" placeholder="{{ __('Bank Name') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <input type="text" name="payment_details[iban]" placeholder="{{ __('IBAN / Account Number') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div x-show="method === 'vodafone'" class="space-y-2">
                <input type="text" name="payment_details[phone]" placeholder="{{ __('Vodafone Cash Number') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div x-show="method === 'instapay'" class="space-y-2">
                <input type="text" name="payment_details[handle]" placeholder="{{ __('InstaPay Handle / Phone') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('payout-modal').classList.add('hidden')"
                    class="px-4 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-100">{{ __('Cancel') }}</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium bg-amber-600 hover:bg-amber-700 text-white">
                    {{ __('Submit Request') }}
                </button>
            </div>
        </form>
    </div>
</div>
