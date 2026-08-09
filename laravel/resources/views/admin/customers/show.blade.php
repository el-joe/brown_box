@extends('admin.layouts.app')

@section('title', $customer->name)

@section('breadcrumb')
    <a href="{{ route('admin.customers.index') }}" class="text-slate-400 hover:text-slate-600">{{ __('Customers') }}</a>
    <span class="mx-1 text-slate-300">/</span>
    <span>{{ $customer->name }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            @if ($customer->avatar)
                <img src="{{ asset_url($customer->avatar) }}" class="w-12 h-12 rounded-full object-cover border border-slate-200">
            @else
                <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-lg font-semibold">
                    {{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-lg font-semibold text-slate-800">{{ $customer->name }}</h1>
                <div class="text-xs text-slate-500">{{ __('Registered') }} {{ $customer->created_at->format('Y-m-d') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $customer->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                {{ $customer->is_active ? __('Active') : __('Inactive') }}
            </span>
            <button type="button" @click="$dispatch('open-modal-edit-customer')" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                <i class="fa-solid fa-pen me-1"></i>{{ __('Edit') }}
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Profile Info --}}
            <x-admin.card :title="__('Profile Info')">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Email') }}</div>
                        <div class="font-medium text-slate-800">{{ $customer->email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Phone') }}</div>
                        <div class="font-medium text-slate-800">{{ $customer->phone ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Orders') }}</div>
                        <div class="font-medium text-slate-800">{{ $customer->orders_count }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Total Spent') }}</div>
                        <div class="font-medium text-slate-800">{{ money_format((float) ($customer->total_spent ?? 0)) }}</div>
                    </div>
                </div>
            </x-admin.card>

            {{-- Addresses --}}
            <x-admin.card :title="__('Addresses')">
                @forelse ($customer->addresses as $address)
                    <div class="text-sm border-b border-slate-100 py-2 last:border-b-0">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-slate-800">{{ $address->label ?? __('Address') }}</span>
                            @if ($address->is_default)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">{{ __('Default') }}</span>
                            @endif
                        </div>
                        <div class="text-slate-500">{{ $address->name }} — {{ $address->phone }}</div>
                        <div class="text-slate-500">{{ $address->address_line }}, {{ $address->city?->name }}, {{ $address->governorate?->name }}</div>
                    </div>
                @empty
                    <div class="text-sm text-slate-400">{{ __('No addresses on file.') }}</div>
                @endforelse
            </x-admin.card>

            {{-- Orders History --}}
            <x-admin.card :title="__('Orders History')">
                <x-admin.table
                    id="customer-orders-table"
                    :ajax-url="route('admin.customers.orders-data', $customer)"
                    :columns="[
                        ['data' => 'order_number', 'name' => 'order_number', 'title' => __('Order #')],
                        ['data' => 'date', 'name' => 'created_at', 'title' => __('Date')],
                        ['data' => 'total', 'name' => 'total_amount', 'title' => __('Total')],
                        ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
                        ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
                    ]"
                />
            </x-admin.card>

            {{-- Activity Timeline --}}
            <x-admin.card :title="__('Activity Timeline')">
                <ol class="relative border-s border-slate-200 ms-2">
                    <li class="mb-6 ms-4">
                        <div class="absolute w-2.5 h-2.5 bg-amber-500 rounded-full mt-1.5 -start-1.25 border border-white"></div>
                        <time class="mb-1 text-xs font-normal text-slate-400">{{ $customer->created_at->format('Y-m-d H:i') }}</time>
                        <div class="text-sm font-medium text-slate-800">{{ __('Account created') }}</div>
                    </li>
                    @foreach ($customer->orders()->latest()->limit(10)->get() as $order)
                        <li class="mb-6 ms-4">
                            <div class="absolute w-2.5 h-2.5 bg-slate-300 rounded-full mt-1.5 -start-1.25 border border-white"></div>
                            <time class="mb-1 text-xs font-normal text-slate-400">{{ $order->created_at->format('Y-m-d H:i') }}</time>
                            <div class="text-sm font-medium text-slate-800">{{ __('Placed order') }} {{ $order->order_number }}</div>
                            <div class="text-sm text-slate-500">{{ money_format((float) $order->total_amount) }}</div>
                        </li>
                    @endforeach
                </ol>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            {{-- Affiliate Info --}}
            <x-admin.card :title="__('Affiliate Info')">
                @if ($customer->affiliate)
                    <div class="text-sm space-y-2">
                        <div class="flex justify-between"><span class="text-slate-500">{{ __('Code') }}</span><span class="font-medium">{{ $customer->affiliate->code }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">{{ __('Balance') }}</span><span class="font-medium">{{ money_format((float) $customer->affiliate->balance) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">{{ __('Total Earned') }}</span><span class="font-medium">{{ money_format((float) $customer->affiliate->total_earned) }}</span></div>
                        <a href="#" class="inline-block mt-2 text-amber-600 hover:underline">{{ __('View Affiliate Record') }}</a>
                    </div>
                @else
                    <div class="text-sm text-slate-400">{{ __('This customer is not an affiliate.') }}</div>
                @endif
            </x-admin.card>

            {{-- Balance / Credits --}}
            <x-admin.card :title="__('Balance / Credits')">
                @if ($customer->affiliate)
                    <div class="text-2xl font-semibold text-slate-800">{{ money_format((float) $customer->affiliate->balance) }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ __('Available affiliate balance') }}</div>
                @else
                    <div class="text-sm text-slate-400">{{ __('No balance or store credits.') }}</div>
                @endif
            </x-admin.card>
        </div>
    </div>

    {{-- Edit Customer Modal --}}
    <x-admin.modal id="edit-customer" :header="__('Edit Customer')">
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
            @csrf
            @method('PUT')
            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" required value="{{ $customer->name }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Email') }}</label>
                <input type="email" name="email" required value="{{ $customer->email }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Phone') }}</label>
                <input type="text" name="phone" value="{{ $customer->phone }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mb-4 flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" @checked($customer->is_active) class="rounded border-slate-300">
                <label for="is_active" class="text-sm text-slate-700">{{ __('Active') }}</label>
            </div>
            <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                {{ __('Save Changes') }}
            </button>
        </form>
    </x-admin.modal>
@endsection
