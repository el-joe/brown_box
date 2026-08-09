@extends('admin.layouts.app')

@section('title', __('Refund Requests'))

@section('breadcrumb')
    <span>{{ __('Refund Requests') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Refund Requests') }}</h1>
    </div>

    <x-admin.filter-card class="mb-6">
        <form method="GET" action="{{ route('admin.refunds.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Order #') }}</label>
                <input type="text" name="order_number" value="{{ $filters['order_number'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Customer (name/email/phone)') }}</label>
                <input type="text" name="customer" value="{{ $filters['customer'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                <x-admin.select name="status" :options="[
                    'pending' => __('Pending'), 'approved' => __('Approved'),
                    'processed' => __('Processed'), 'rejected' => __('Rejected'),
                ]" :selected="$filters['status'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <x-admin.table
        id="refunds-table"
        :ajax-url="route('admin.refunds.data', array_filter($filters))"
        :columns="[
            ['data' => 'order_number', 'name' => 'order.order_number', 'title' => __('Order #'), 'orderable' => false],
            ['data' => 'customer', 'name' => 'customer.name', 'title' => __('Customer'), 'orderable' => false],
            ['data' => 'amount', 'name' => 'refund_amount', 'title' => __('Amount')],
            ['data' => 'reason', 'name' => 'reason', 'title' => __('Reason')],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'submitted_at', 'name' => 'created_at', 'title' => __('Submitted At')],
            ['data' => 'processed_at', 'name' => 'processed_at', 'title' => __('Processed At')],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    />
@endsection
