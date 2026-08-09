@extends('admin.layouts.app')

@section('title', $affiliate->customer?->name ?? $affiliate->code)

@section('breadcrumb')
    <a href="{{ route('admin.affiliates.index') }}" class="hover:text-slate-700">{{ __('Affiliates') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $affiliate->customer?->name ?? $affiliate->code }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">{{ $affiliate->customer?->name ?? '—' }}</h1>
            <div class="text-xs text-slate-500">{{ $affiliate->customer?->email }} &middot; {{ __('Code') }}: {{ $affiliate->code }}</div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $affiliate->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                {{ $affiliate->is_active ? __('Active') : __('Inactive') }}
            </span>
            <button type="button" @click="$dispatch('open-modal-add-commission')" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-plus me-1"></i>{{ __('Add Manual Commission') }}
            </button>
            <a href="{{ route('admin.affiliates.edit', $affiliate) }}" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                <i class="fa-solid fa-pen me-1"></i>{{ __('Edit') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Balance') }}</div>
            <div class="text-xl font-semibold text-slate-800">{{ money_format((float) $affiliate->balance) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Total Earned') }}</div>
            <div class="text-xl font-semibold text-slate-800">{{ money_format((float) $affiliate->total_earned) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Pending Commissions') }}</div>
            <div class="text-xl font-semibold text-slate-800">{{ money_format((float) $pendingCommissions) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-xs text-slate-500">{{ __('Total Orders') }}</div>
            <div class="text-xl font-semibold text-slate-800">{{ $affiliate->orders()->count() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('Profile')">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Commission Type') }}</div>
                        @include('admin.affiliates._commission_type')
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Approved At') }}</div>
                        <div class="font-medium text-slate-800">{{ optional($affiliate->approved_at)->format('Y-m-d H:i') ?? '—' }}</div>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Commissions')">
                <x-admin.table
                    id="affiliate-commissions-table"
                    :ajax-url="route('admin.affiliates.commissions-data', $affiliate)"
                    :columns="[
                        ['data' => 'order_number', 'name' => 'order_number', 'title' => __('Order #'), 'orderable' => false, 'searchable' => false],
                        ['data' => 'order_total', 'name' => 'order_total', 'title' => __('Order Total'), 'orderable' => false, 'searchable' => false],
                        ['data' => 'amount', 'name' => 'amount', 'title' => __('Commission Amount'), 'orderable' => false, 'searchable' => false],
                        ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
                        ['data' => 'available_at', 'name' => 'available_at', 'title' => __('Available At'), 'orderable' => false, 'searchable' => false],
                        ['data' => 'paid_at', 'name' => 'paid_at', 'title' => __('Paid At'), 'orderable' => false, 'searchable' => false],
                    ]"
                />
            </x-admin.card>

            <x-admin.card :title="__('Payout Requests')">
                @forelse ($affiliate->payoutRequests()->latest()->get() as $payout)
                    <div class="text-sm border-b border-slate-100 py-2 last:border-b-0 flex items-center justify-between">
                        <div>
                            <div class="font-medium text-slate-800">{{ money_format((float) $payout->amount) }}</div>
                            <div class="text-slate-500">{{ $payout->payment_method ?? '—' }} &middot; {{ $payout->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        @include('admin.affiliates._payout_status', ['payout' => $payout])
                    </div>
                @empty
                    <div class="text-sm text-slate-400">{{ __('No payout requests yet.') }}</div>
                @endforelse
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Category Commissions')">
                @forelse ($affiliate->categoryCommissions as $cc)
                    <div class="text-sm border-b border-slate-100 py-2 last:border-b-0">
                        <div class="font-medium text-slate-800">{{ $cc->category?->getTranslation('name', app()->getLocale()) }}</div>
                        <div class="text-slate-500">
                            {{ $cc->tier_type === 'tiered' ? __('Tiered') : __('Fixed').': '.rtrim(rtrim(number_format((float) $cc->rate, 2), '0'), '.').'%' }}
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-400">{{ __('No category commissions configured.') }}</div>
                @endforelse
            </x-admin.card>
        </div>
    </div>

    <x-admin.modal id="add-commission" :header="__('Add Manual Commission')">
        <form method="POST" action="{{ route('admin.affiliates.manual-commission', $affiliate) }}">
            @csrf
            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Amount') }}</label>
                <input type="number" min="0.01" step="0.01" name="amount" required class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                {{ __('Add Commission') }}
            </button>
        </form>
    </x-admin.modal>
@endsection
