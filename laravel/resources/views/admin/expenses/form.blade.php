@php
    $isEdit = $expense->exists;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.expenses.update', $expense) : route('admin.expenses.store') }}" class="max-w-3xl">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <x-admin.card :title="__('Expense Details')">
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Date') }}</label>
                    <input type="date" name="date" value="{{ old('date', $expense->date?->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    @error('date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Expense Category') }}</label>
                    <x-admin.select name="category_id" :options="$categories" :selected="old('category_id', $expense->category_id)" :placeholder="__('Select category')" />
                    @error('category_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description') }}</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('description', $expense->description) }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Amount') }}</label>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    @error('amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Payment Method') }}</label>
                    <x-admin.select name="payment_method" :options="['cash' => __('Cash'), 'bank' => __('Bank'), 'other' => __('Other')]" :selected="old('payment_method', $expense->payment_method ?? 'cash')" />
                    @error('payment_method')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Reference') }}</label>
                    <input type="text" name="reference" value="{{ old('reference', $expense->reference) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    @error('reference')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </x-admin.card>

    <div class="flex items-center gap-2 mt-6">
        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            {{ $isEdit ? __('Update Expense') : __('Save Expense') }}
        </button>
        <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
            {{ __('Cancel') }}
        </a>
    </div>
</form>
