@extends('admin.layouts.app')

@section('title', __('Subscribers'))

@section('breadcrumb')
    <span>{{ __('Subscribers') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">{{ __('Subscribers') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __(':active active of :total total', ['active' => $activeCount, 'total' => $totalCount]) }}</p>
        </div>
        <a href="{{ route('admin.subscribers.compose') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-paper-plane me-1"></i>{{ __('Send Newsletter') }}
        </a>
    </div>

    <x-admin.table
        id="subscribers-table"
        :ajax-url="route('admin.subscribers.data')"
        :columns="[
            ['data' => 'email', 'name' => 'email', 'title' => __('Email')],
            ['data' => 'status', 'name' => 'is_active', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'subscribed_at', 'name' => 'created_at', 'title' => __('Subscribed At'), 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    />
@endsection
