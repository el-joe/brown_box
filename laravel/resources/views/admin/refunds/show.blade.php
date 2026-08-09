@extends('admin.layouts.app')

@section('title', __('Refund Request #:id', ['id' => $refund->id]))

@section('breadcrumb')
    <a href="{{ route('admin.refunds.index') }}" class="text-slate-400 hover:text-slate-600">{{ __('Refund Requests') }}</a>
    <span class="mx-1 text-slate-300">/</span>
    <span>#{{ $refund->id }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">{{ __('Refund Request') }} #{{ $refund->id }}</h1>
            <div class="text-xs text-slate-500 mt-1">{{ $refund->created_at->format('Y-m-d H:i') }}</div>
        </div>
        <x-admin.badge :status="$refund->status" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Summary --}}
            <x-admin.card :title="__('Order Summary')">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Order #') }}</div>
                        <div class="font-medium text-slate-800">
                            <a href="{{ route('admin.orders.show', $refund->order) }}" class="text-amber-600 hover:underline">{{ $refund->order->order_number }}</a>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Order Total') }}</div>
                        <div class="font-medium text-slate-800">{{ money_format((float) $refund->order->total_amount) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Customer') }}</div>
                        <div class="font-medium text-slate-800">
                            <a href="{{ route('admin.customers.show', $refund->customer) }}" class="text-amber-600 hover:underline">{{ $refund->customer?->name }}</a>
                        </div>
                        <div class="text-slate-500">{{ $refund->customer?->email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Payment Gateway') }}</div>
                        <div class="font-medium text-slate-800">{{ $refund->order->payment_gateway ?? '—' }}</div>
                    </div>
                </div>
            </x-admin.card>

            {{-- Refund Details --}}
            <x-admin.card :title="__('Refund Details')">
                <div class="text-sm mb-4">
                    <div class="text-xs text-slate-500 mb-1">{{ __('Reason') }}</div>
                    <div class="text-slate-800 font-medium">{{ $refund->reason }}</div>
                </div>

                @if ($refund->details)
                    <div class="text-sm mb-4">
                        <div class="text-xs text-slate-500 mb-1">{{ __('Description') }}</div>
                        <div class="text-slate-700">{{ $refund->details }}</div>
                    </div>
                @endif

                @if (! empty($refund->photos))
                    <div class="text-sm">
                        <div class="text-xs text-slate-500 mb-2">{{ __('Photos') }}</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($refund->photos as $photo)
                                <a href="{{ asset_url($photo) }}" target="_blank">
                                    <img src="{{ asset_url($photo) }}" class="w-24 h-24 object-cover rounded-lg border border-slate-200">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($refund->admin_notes)
                    <div class="text-sm mt-4 pt-4 border-t border-slate-100">
                        <div class="text-xs text-slate-500 mb-1">{{ __('Admin Notes') }}</div>
                        <div class="text-slate-700">{{ $refund->admin_notes }}</div>
                    </div>
                @endif

                @if ($refund->processed_at)
                    <div class="text-xs text-slate-400 mt-3">{{ __('Processed at') }} {{ $refund->processed_at->format('Y-m-d H:i') }}</div>
                @endif
            </x-admin.card>

            @if ($refund->status === \App\Enums\RefundStatus::Pending)
                {{-- Approve Form --}}
                <x-admin.card :title="__('Approve Refund')">
                    <form method="POST" action="{{ route('admin.refunds.approve', $refund) }}">
                        @csrf
                        <div class="admin-field mb-3">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Refund Amount') }}</label>
                            <input type="number" step="0.01" min="0.01" name="refund_amount" required value="{{ $refund->refund_amount ?? $refund->order->total_amount }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="admin-field mb-4">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Notes') }}</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                            <i class="fa-solid fa-check me-1"></i>{{ __('Approve Refund') }}
                        </button>
                    </form>
                </x-admin.card>

                {{-- Reject Form --}}
                <x-admin.card :title="__('Reject Refund')">
                    <form method="POST" action="{{ route('admin.refunds.reject', $refund) }}">
                        @csrf
                        <div class="admin-field mb-4">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Rejection Notes') }}</label>
                            <textarea name="notes" rows="3" required class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                            <i class="fa-solid fa-xmark me-1"></i>{{ __('Reject Refund') }}
                        </button>
                    </form>
                </x-admin.card>
            @endif
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Summary')">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Status') }}</span><x-admin.badge :status="$refund->status" /></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Refund Amount') }}</span><span class="font-medium">{{ money_format((float) ($refund->refund_amount ?? 0)) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Submitted') }}</span><span>{{ $refund->created_at->format('Y-m-d H:i') }}</span></div>
                </div>
            </x-admin.card>
        </div>
    </div>
@endsection
