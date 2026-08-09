@extends('admin.layouts.app')

@section('title', __('Order :number', ['number' => $order->order_number]))

@section('breadcrumb')
    <a href="{{ route('admin.orders.index') }}" class="text-slate-400 hover:text-slate-600">{{ __('Orders') }}</a>
    <span class="mx-1 text-slate-300">/</span>
    <span>{{ $order->order_number }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">{{ __('Order') }} {{ $order->order_number }}</h1>
            <div class="text-xs text-slate-500 mt-1">{{ $order->created_at->format('Y-m-d H:i') }}</div>
        </div>
        <div class="flex items-center gap-2">
            <x-admin.badge :status="$order->status" />
            <x-admin.badge :status="$order->payment_status" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Section 1: Summary --}}
            <x-admin.card :title="__('Order Summary')">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Customer') }}</div>
                        <div class="font-medium text-slate-800">{{ $order->customer?->name ?? __('Guest') }}</div>
                        <div class="text-slate-500">{{ $order->customer?->email }}</div>
                        <div class="text-slate-500">{{ $order->customer?->phone }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Shipping Address') }}</div>
                        <div class="text-slate-700">{{ $order->customer_address['address_line'] ?? '—' }}</div>
                    </div>
                </div>
            </x-admin.card>

            {{-- Section 2: Items --}}
            <x-admin.card :title="__('Items')">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                                <th class="py-2">{{ __('Product') }}</th>
                                <th class="py-2">{{ __('Variant') }}</th>
                                <th class="py-2 text-right">{{ __('Qty') }}</th>
                                <th class="py-2 text-right">{{ __('Unit Price') }}</th>
                                <th class="py-2 text-right">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 flex items-center gap-2">
                                        @if ($item->product?->main_image)
                                            <img src="{{ asset_url($item->product->main_image->path) }}" class="w-8 h-8 rounded object-cover border border-slate-200">
                                        @endif
                                        {{ $item->product_name }}
                                    </td>
                                    <td class="py-2 text-slate-500">{{ $item->variant_label ?? '—' }}</td>
                                    <td class="py-2 text-right">{{ $item->qty }}</td>
                                    <td class="py-2 text-right">{{ money_format((float) $item->unit_price) }}</td>
                                    <td class="py-2 text-right font-medium">{{ money_format((float) $item->total_price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.card>

            {{-- Section 3: Pricing --}}
            <x-admin.card :title="__('Pricing Summary')">
                <div class="space-y-2 text-sm max-w-xs ms-auto">
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Subtotal') }}</span><span>{{ money_format((float) $order->subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Coupon Discount') }}</span><span>-{{ money_format((float) $order->coupon_discount) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Shipping') }}</span><span>{{ money_format((float) $order->shipping_amount) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Tax') }}</span><span>{{ money_format((float) $order->tax_amount) }}</span></div>
                    <div class="flex justify-between font-semibold text-slate-800 pt-2 border-t border-slate-200"><span>{{ __('Total') }}</span><span>{{ money_format((float) $order->total_amount) }}</span></div>
                </div>
            </x-admin.card>

            {{-- Section 4: Payment Info --}}
            <x-admin.card :title="__('Payment Info')">
                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Gateway') }}</div>
                        <div>{{ $order->payment_gateway ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Reference') }}</div>
                        <div>{{ $order->payment_reference ?? '—' }}</div>
                    </div>
                </div>

                @if ($order->payment_proof)
                    <div class="mb-4">
                        <div class="text-xs text-slate-500 mb-1">{{ __('Payment Proof') }}</div>
                        <a href="{{ asset_url($order->payment_proof) }}" target="_blank">
                            <img src="{{ asset_url($order->payment_proof) }}" class="max-w-xs rounded-lg border border-slate-200">
                        </a>
                    </div>
                @endif

                @if ($order->payment_status === 'pending_verification' || $order->payment_status === 'unpaid')
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.orders.verify-payment', $order) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                                <i class="fa-solid fa-check me-1"></i>{{ __('Verify Payment') }}
                            </button>
                        </form>
                        <button type="button" @click="$dispatch('open-modal-reject-payment')" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                            <i class="fa-solid fa-xmark me-1"></i>{{ __('Reject Payment') }}
                        </button>
                    </div>
                @endif
            </x-admin.card>

            {{-- Section 5: Shipping --}}
            <x-admin.card :title="__('Shipping')">
                <form method="POST" action="{{ route('admin.orders.assign-shipping', $order) }}" class="grid grid-cols-2 gap-4 mb-4">
                    @csrf
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Shipping Company') }}</label>
                        <x-admin.select name="shipping_company_id" :options="$shippingCompanies" :selected="$order->shipping_company_id" :placeholder="__('None')" />
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Tracking Number') }}</label>
                        <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="col-span-2">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                            {{ __('Save Shipping Details') }}
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.orders.shipping-status', $order) }}" class="flex items-end gap-3">
                    @csrf
                    <div class="admin-field flex-1">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Shipping Status') }}</label>
                        <x-admin.select name="shipping_status" :options="[
                            'pending' => __('Pending'), 'picked_up' => __('Picked Up'), 'in_transit' => __('In Transit'),
                            'delivered' => __('Delivered'), 'returned' => __('Returned'),
                        ]" :selected="$order->shipping_status" />
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Update') }}
                    </button>
                </form>
            </x-admin.card>

            {{-- Section 6: Order Status --}}
            <x-admin.card :title="__('Order Status')">
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
                    @csrf
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                        <x-admin.select name="status" :options="[
                            'pending' => __('Pending'), 'confirmed' => __('Confirmed'), 'processing' => __('Processing'),
                            'shipped' => __('Shipped'), 'delivered' => __('Delivered'), 'cancelled' => __('Cancelled'), 'refunded' => __('Refunded'),
                        ]" :selected="$order->status" />
                    </div>
                    <div class="admin-field md:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Notes') }}</label>
                        <input type="text" name="notes" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                            {{ __('Change Status') }}
                        </button>
                    </div>
                </form>

                <div class="text-xs font-medium text-slate-500 mb-3">{{ __('Status Timeline') }}</div>
                <ol class="relative border-s border-slate-200 ms-2">
                    @forelse ($order->statusHistories()->orderByDesc('created_at')->get() as $history)
                        <li class="mb-6 ms-4">
                            <div class="absolute w-2.5 h-2.5 bg-amber-500 rounded-full mt-1.5 -start-1.25 border border-white"></div>
                            <time class="mb-1 text-xs font-normal text-slate-400">{{ $history->created_at->format('Y-m-d H:i') }}</time>
                            <div class="text-sm font-medium text-slate-800">{{ ucfirst($history->status) }}</div>
                            @if ($history->notes)
                                <div class="text-sm text-slate-500">{{ $history->notes }}</div>
                            @endif
                            @if ($history->admin)
                                <div class="text-xs text-slate-400">{{ __('by') }} {{ $history->admin->name }}</div>
                            @endif
                        </li>
                    @empty
                        <li class="ms-4 text-sm text-slate-400">{{ __('No history yet.') }}</li>
                    @endforelse
                </ol>
            </x-admin.card>

            {{-- Section 7: Affiliate --}}
            @if ($order->affiliate_id)
                <x-admin.card :title="__('Affiliate Commission')">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-xs text-slate-500">{{ __('Affiliate') }}</div>
                            <div>{{ $order->affiliate?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">{{ __('Commission Amount') }}</div>
                            <div>{{ money_format((float) $order->affiliate_commission) }}</div>
                        </div>
                    </div>
                    @if ($order->affiliateCommissions->isNotEmpty())
                        <div class="mt-3 text-xs text-slate-500">
                            {{ __('Status') }}: {{ ucfirst($order->affiliateCommissions->first()->status) }}
                        </div>
                    @endif
                </x-admin.card>
            @endif

            {{-- Section 8: Admin Notes --}}
            <x-admin.card :title="__('Admin Notes (Internal)')">
                <form method="POST" action="{{ route('admin.orders.admin-notes', $order) }}">
                    @csrf
                    <textarea name="admin_notes" rows="3" class="w-full rounded-lg border-slate-300 text-sm mb-3" placeholder="{{ __('Internal notes, not visible to customer...') }}">{{ $order->admin_notes }}</textarea>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Save Notes') }}
                    </button>
                </form>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            {{-- Section 9: Actions --}}
            <x-admin.card :title="__('Actions')">
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 text-center">
                        <i class="fa-solid fa-print me-1"></i>{{ __('Print Invoice') }}
                    </a>
                    <form method="POST" action="{{ route('admin.orders.send-invoice', $order) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            <i class="fa-solid fa-envelope me-1"></i>{{ __('Send Invoice to Customer') }}
                        </button>
                    </form>
                    <button type="button" @click="$dispatch('open-modal-create-refund')" class="w-full px-4 py-2 rounded-lg border border-red-200 text-sm font-medium text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-rotate-left me-1"></i>{{ __('Create Refund Request') }}
                    </button>
                </div>
            </x-admin.card>

            @if ($order->refundRequests->isNotEmpty())
                <x-admin.card :title="__('Refund Requests')">
                    <div class="space-y-3">
                        @foreach ($order->refundRequests as $refund)
                            <div class="text-sm border-b border-slate-100 pb-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">{{ money_format((float) $refund->refund_amount) }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ ucfirst($refund->status) }}</span>
                                </div>
                                <div class="text-slate-500 text-xs">{{ $refund->reason }}</div>
                            </div>
                        @endforeach
                    </div>
                </x-admin.card>
            @endif
        </div>
    </div>

    {{-- Reject Payment Modal --}}
    <x-admin.modal id="reject-payment" :header="__('Reject Payment')">
        <form method="POST" action="{{ route('admin.orders.reject-payment', $order) }}">
            @csrf
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Reason (sent to customer)') }}</label>
            <textarea name="notes" rows="3" required class="w-full rounded-lg border-slate-300 text-sm mb-4"></textarea>
            <button type="submit" class="w-full px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                {{ __('Reject Payment') }}
            </button>
        </form>
    </x-admin.modal>

    {{-- Create Refund Modal --}}
    <x-admin.modal id="create-refund" :header="__('Create Refund Request')">
        <form method="POST" action="{{ route('admin.orders.refund', $order) }}">
            @csrf
            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Reason') }}</label>
                <input type="text" name="reason" required class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Details') }}</label>
                <textarea name="details" rows="3" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
            </div>
            <div class="admin-field mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Refund Amount') }}</label>
                <input type="number" step="0.01" name="refund_amount" required value="{{ $order->total_amount }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <button type="submit" class="w-full px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                {{ __('Submit Refund Request') }}
            </button>
        </form>
    </x-admin.modal>
@endsection
