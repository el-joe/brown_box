@extends('admin.layouts.app')

@section('title', __('POS'))

@section('breadcrumb')
    <a href="{{ route('admin.orders.index') }}" class="hover:text-slate-700">{{ __('Orders') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('POS') }}</span>
@endsection

@push('styles')
    <style>
        @media print {
            body * { visibility: hidden; }
            #receipt, #receipt * { visibility: visible; }
            #receipt { position: absolute; top: 0; left: 0; width: 280px; font-family: monospace; font-size: 12px; }
        }
    </style>
@endpush

@section('content')
    <div
        x-data="posScreen({
            searchProductsUrl: @js(route('admin.orders.pos.search-products')),
            searchCustomersUrl: @js(route('admin.orders.pos.search-customers')),
            quickAddCustomerUrl: @js(route('admin.orders.pos.quick-add-customer')),
            shippingCostUrl: @js(route('admin.orders.pos.shipping-cost')),
            storeUrl: @js(route('admin.orders.pos.store')),
        })"
        x-init="init()"
        class="grid grid-cols-1 lg:grid-cols-3 gap-6"
    >
        {{-- Left panel: product search + grid --}}
        <div class="lg:col-span-2 space-y-4">
            <x-admin.card>
                <input
                    type="text"
                    x-model="keyword"
                    @input.debounce.300ms="searchProducts()"
                    placeholder="{{ __('Search products by name or SKU...') }}"
                    class="w-full rounded-lg border-slate-300 text-sm mb-3"
                >

                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" @click="categoryId = null; searchProducts()" :class="!categoryId ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600'" class="px-3 py-1.5 rounded-full text-xs font-medium">
                        {{ __('All') }}
                    </button>
                    @foreach ($categories as $category)
                        <button type="button" @click="categoryId = {{ $category->id }}; searchProducts()" :class="categoryId === {{ $category->id }} ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600'" class="px-3 py-1.5 rounded-full text-xs font-medium">
                            {{ $category->getTranslation('name', app()->getLocale()) }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                    <template x-for="product in products" :key="product.id">
                        <button type="button" @click="addToCart(product)" class="border border-slate-200 rounded-xl p-3 text-start hover:border-amber-400 hover:shadow-sm transition">
                            <div class="w-full aspect-square rounded-lg bg-slate-100 mb-2 flex items-center justify-center overflow-hidden">
                                <img x-show="product.image" :src="product.image" class="w-full h-full object-cover">
                                <i x-show="!product.image" class="fa-solid fa-box text-slate-300 text-2xl"></i>
                            </div>
                            <div class="text-xs font-medium text-slate-800 line-clamp-2" x-text="product.name"></div>
                            <div class="text-xs text-amber-600 font-semibold mt-1" x-text="formatMoney(product.price)"></div>
                        </button>
                    </template>
                </div>

                <div x-show="!products.length" class="text-center text-sm text-slate-400 py-10">
                    {{ __('No products found.') }}
                </div>
            </x-admin.card>
        </div>

        {{-- Right panel: cart --}}
        <div class="space-y-4">
            <x-admin.card :title="__('Customer')">
                <div class="flex gap-2 mb-2">
                    <input type="text" x-model="customerKeyword" @input.debounce.300ms="searchCustomers()" placeholder="{{ __('Search customer...') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    <button type="button" @click="showQuickAddCustomer = true" class="px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50 whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>

                <ul x-show="customerResults.length" class="border border-slate-200 rounded-lg divide-y divide-slate-100 mb-2 max-h-40 overflow-y-auto">
                    <template x-for="c in customerResults" :key="c.id">
                        <li @click="selectCustomer(c)" class="px-3 py-2 text-sm hover:bg-slate-50 cursor-pointer">
                            <span x-text="c.name"></span> — <span class="text-slate-400" x-text="c.phone"></span>
                        </li>
                    </template>
                </ul>

                <div x-show="customer" class="text-sm bg-slate-50 rounded-lg px-3 py-2 flex items-center justify-between">
                    <span><span x-text="customer?.name"></span> (<span x-text="customer?.phone"></span>)</span>
                    <button type="button" @click="customer = null" class="text-slate-400 hover:text-red-600"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div x-show="showQuickAddCustomer" class="mt-3 space-y-2 border-t border-slate-100 pt-3">
                    <input type="text" x-model="newCustomer.name" placeholder="{{ __('Name') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    <input type="text" x-model="newCustomer.phone" placeholder="{{ __('Phone') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    <input type="email" x-model="newCustomer.email" placeholder="{{ __('Email (optional)') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    <button type="button" @click="quickAddCustomer()" class="w-full px-3 py-2 rounded-lg bg-slate-800 text-white text-xs font-medium">
                        {{ __('Add Customer') }}
                    </button>
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Cart')">
                <div x-show="!cart.length" class="text-center text-sm text-slate-400 py-6">{{ __('Cart is empty.') }}</div>

                <div class="space-y-3 max-h-64 overflow-y-auto">
                    <template x-for="(line, index) in cart" :key="line.key">
                        <div class="flex items-center justify-between text-sm border-b border-slate-100 pb-2">
                            <div class="flex-1">
                                <div class="font-medium text-slate-800" x-text="line.name"></div>
                                <div class="text-xs text-slate-400" x-text="formatMoney(line.price) + ' ' + '{{ __('each') }}'"></div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="decrementQty(index)" class="w-6 h-6 rounded-full border border-slate-300 text-xs">-</button>
                                <span class="w-6 text-center" x-text="line.qty"></span>
                                <button type="button" @click="incrementQty(index)" class="w-6 h-6 rounded-full border border-slate-300 text-xs">+</button>
                            </div>
                            <button type="button" @click="removeFromCart(index)" class="text-slate-300 hover:text-red-600 ms-2"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </template>
                </div>

                <div class="mt-4 space-y-2">
                    <div class="flex gap-2">
                        <input type="text" x-model="couponCode" placeholder="{{ __('Coupon code') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>

                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Shipping Company') }}</label>
                        <select x-model="shippingCompanyId" @change="calculateShipping()" class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="">{{ __('None (pickup)') }}</option>
                            @foreach ($shippingCompanies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="admin-field">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Governorate') }}</label>
                            <select x-model="governorateId" @change="cityId = null" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">—</option>
                                @foreach ($governorates as $gov)
                                    <option value="{{ $gov->id }}">{{ $gov->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-field">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('City') }}</label>
                            <select x-model="cityId" @change="calculateShipping()" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">—</option>
                                @foreach ($governorates as $gov)
                                    @foreach ($gov->cities as $city)
                                        <option value="{{ $city->id }}" x-show="governorateId == {{ $gov->id }}">{{ $city->name_en }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Warehouse') }}</label>
                        <select x-model="warehouseId" class="w-full rounded-lg border-slate-300 text-sm">
                            @foreach ($warehouses as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Payment Method') }}</label>
                        <select x-model="paymentGateway" class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="vodafone_cash">{{ __('Vodafone Cash') }}</option>
                            <option value="instapay">{{ __('Instapay') }}</option>
                            <option value="paymob">{{ __('Paymob') }}</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 space-y-1 text-sm border-t border-slate-200 pt-3">
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Subtotal') }}</span><span x-text="formatMoney(subtotal())"></span></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Shipping') }}</span><span x-text="formatMoney(shippingAmount)"></span></div>
                    <div class="flex justify-between font-semibold text-slate-800"><span>{{ __('Total') }}</span><span x-text="formatMoney(grandTotal())"></span></div>
                </div>

                <button type="button" @click="placeOrder()" :disabled="!cart.length || !customer || placing" class="w-full mt-4 px-4 py-3 rounded-lg bg-amber-600 text-white text-sm font-semibold hover:bg-amber-700 disabled:opacity-50">
                    <span x-show="!placing">{{ __('Place Order') }}</span>
                    <span x-show="placing">{{ __('Placing...') }}</span>
                </button>

                <button type="button" x-show="lastOrder" @click="printReceipt()" class="w-full mt-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <i class="fa-solid fa-print me-1"></i>{{ __('Print Receipt') }}
                </button>
            </x-admin.card>
        </div>

        {{-- Thermal receipt (hidden, print only) --}}
        <div id="receipt" class="hidden">
            <div style="text-align:center;">
                <strong>{{ config('app.name') }}</strong><br>
                <span x-text="lastOrder?.order_number"></span>
            </div>
            <hr>
            <template x-for="line in receiptCart" :key="line.key">
                <div style="display:flex; justify-content:space-between;">
                    <span x-text="line.name + ' x' + line.qty"></span>
                    <span x-text="formatMoney(line.price * line.qty)"></span>
                </div>
            </template>
            <hr>
            <div style="display:flex; justify-content:space-between;"><span>{{ __('Total') }}</span><span x-text="formatMoney(receiptTotal)"></span></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function posScreen({ searchProductsUrl, searchCustomersUrl, quickAddCustomerUrl, shippingCostUrl, storeUrl }) {
            return {
                keyword: '',
                categoryId: null,
                products: [],
                cart: [],
                customer: null,
                customerKeyword: '',
                customerResults: [],
                showQuickAddCustomer: false,
                newCustomer: { name: '', phone: '', email: '' },
                couponCode: '',
                shippingCompanyId: '',
                governorateId: '',
                cityId: '',
                shippingAmount: 0,
                warehouseId: @js(array_key_first($warehouses->toArray()) ?? ''),
                paymentGateway: 'cash',
                placing: false,
                lastOrder: null,
                receiptCart: [],
                receiptTotal: 0,

                init() {
                    this.searchProducts();
                },

                csrfHeaders(json = true) {
                    const headers = {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        Accept: 'application/json',
                    };
                    if (json) headers['Content-Type'] = 'application/json';
                    return headers;
                },

                async searchProducts() {
                    const params = new URLSearchParams({ q: this.keyword });
                    if (this.categoryId) params.set('category_id', this.categoryId);
                    const response = await fetch(`${searchProductsUrl}?${params}`, { headers: this.csrfHeaders(false) });
                    this.products = await response.json();
                },

                addToCart(product) {
                    const variant = product.has_variants && product.variants.length ? product.variants[0] : null;
                    const key = product.id + ':' + (variant?.id ?? 0);
                    const existing = this.cart.find((l) => l.key === key);

                    if (existing) {
                        existing.qty += 1;
                        return;
                    }

                    this.cart.push({
                        key,
                        product_id: product.id,
                        variant_id: variant?.id ?? null,
                        name: product.name,
                        price: variant?.price ?? product.price,
                        qty: 1,
                    });
                },

                incrementQty(index) {
                    this.cart[index].qty += 1;
                },

                decrementQty(index) {
                    if (this.cart[index].qty > 1) {
                        this.cart[index].qty -= 1;
                    } else {
                        this.removeFromCart(index);
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                subtotal() {
                    return this.cart.reduce((sum, l) => sum + l.price * l.qty, 0);
                },

                grandTotal() {
                    return Math.max(0, this.subtotal() + (Number(this.shippingAmount) || 0));
                },

                formatMoney(amount) {
                    return (Number(amount) || 0).toFixed(2) + ' {{ setting('currency', 'EGP') }}';
                },

                async searchCustomers() {
                    if (!this.customerKeyword) {
                        this.customerResults = [];
                        return;
                    }
                    const response = await fetch(`${searchCustomersUrl}?q=${encodeURIComponent(this.customerKeyword)}`, { headers: this.csrfHeaders(false) });
                    this.customerResults = await response.json();
                },

                selectCustomer(c) {
                    this.customer = c;
                    this.customerResults = [];
                    this.customerKeyword = '';
                },

                async quickAddCustomer() {
                    const response = await fetch(quickAddCustomerUrl, {
                        method: 'POST',
                        headers: this.csrfHeaders(),
                        body: JSON.stringify(this.newCustomer),
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.selectCustomer(data);
                        this.showQuickAddCustomer = false;
                        this.newCustomer = { name: '', phone: '', email: '' };
                    } else {
                        alert(data.message || @js(__('Could not add customer.')));
                    }
                },

                async calculateShipping() {
                    if (!this.shippingCompanyId) {
                        this.shippingAmount = 0;
                        return;
                    }
                    const response = await fetch(shippingCostUrl, {
                        method: 'POST',
                        headers: this.csrfHeaders(),
                        body: JSON.stringify({
                            shipping_company_id: this.shippingCompanyId,
                            governorate_id: this.governorateId || null,
                            city_id: this.cityId || null,
                        }),
                    });
                    if (response.ok) {
                        const data = await response.json();
                        this.shippingAmount = data.cost;
                    }
                },

                async placeOrder() {
                    if (!this.cart.length || !this.customer) return;
                    this.placing = true;

                    try {
                        const response = await fetch(storeUrl, {
                            method: 'POST',
                            headers: this.csrfHeaders(),
                            body: JSON.stringify({
                                customer_id: this.customer.id,
                                warehouse_id: this.warehouseId,
                                items: this.cart.map((l) => ({
                                    product_id: l.product_id,
                                    variant_id: l.variant_id,
                                    qty: l.qty,
                                    unit_price: l.price,
                                })),
                                coupon_code: this.couponCode || null,
                                shipping_company_id: this.shippingCompanyId || null,
                                shipping_amount: this.shippingAmount,
                                payment_gateway: this.paymentGateway,
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            alert(data.message || @js(__('Could not place order.')));
                            return;
                        }

                        this.lastOrder = data;
                        this.receiptCart = this.cart;
                        this.receiptTotal = this.grandTotal();
                        this.cart = [];
                        this.couponCode = '';
                        this.shippingAmount = 0;
                    } finally {
                        this.placing = false;
                    }
                },

                printReceipt() {
                    window.print();
                },
            };
        }
    </script>
@endpush
