@extends('admin.layouts.app')

@section('title', __('Payout Requests'))

@section('breadcrumb')
    <a href="{{ route('admin.affiliates.index') }}" class="hover:text-slate-700">{{ __('Affiliates') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Payout Requests') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Payout Requests') }}</h1>
        <a href="{{ route('admin.affiliates.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to Affiliates') }}
        </a>
    </div>

    <x-admin.table
        id="payouts-table"
        :ajax-url="route('admin.affiliates.payouts.data')"
        :columns="[
            ['data' => 'affiliate', 'name' => 'affiliate', 'title' => __('Affiliate'), 'orderable' => false],
            ['data' => 'amount', 'name' => 'amount', 'title' => __('Amount'), 'orderable' => false, 'searchable' => false],
            ['data' => 'method', 'name' => 'method', 'title' => __('Method'), 'orderable' => false, 'searchable' => false],
            ['data' => 'details', 'name' => 'details', 'title' => __('Details'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'requested_at', 'name' => 'created_at', 'title' => __('Requested At'), 'orderable' => false, 'searchable' => false],
            ['data' => 'processed_at', 'name' => 'processed_at', 'title' => __('Processed At'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.affiliates.payouts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Affiliate') }}</label>
                    <input type="text" name="affiliate" value="{{ $filters['affiliate'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="status" :options="[
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'paid' => __('Paid'),
                        'rejected' => __('Rejected'),
                    ]" :selected="$filters['status'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </x-slot:filters>
    </x-admin.table>
@endsection
