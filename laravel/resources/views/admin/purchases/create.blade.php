@extends('admin.layouts.app')

@section('title', __('Create Purchase'))

@section('breadcrumb')
    <a href="{{ route('admin.purchases.index') }}" class="hover:text-slate-700">{{ __('Purchases') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Create') }}</span>
@endsection

@section('content')
    <form
        id="purchase-form"
        method="POST"
        action="{{ route('admin.purchases.store') }}"
        x-data="purchaseForm({ searchUrl: @js(route('admin.purchases.search-products')) })"
        x-init="init()"
    >
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-admin.card :title="__('Purchase Details')">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Supplier') }}</label>
                            <div class="flex gap-2">
                                <select id="supplier-select" name="supplier_id" class="admin-select2 w-full" data-placeholder="{{ __('Select supplier') }}">
                                    <option value=""></option>
                                    @foreach ($suppliers as $id => $name)
                                        <option value="{{ $id }}" @selected(old('supplier_id') == $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="$dispatch('open-modal-quick-add-supplier')" class="px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50 whitespace-nowrap">
                                    <i class="fa-solid fa-plus me-1"></i>{{ __('New') }}
                                </button>
                            </div>
                            @error('supplier_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Warehouse') }}</label>
                            <x-admin.select name="warehouse_id" :options="$warehouses" :selected="old('warehouse_id')" :placeholder="__('Select warehouse')" />
                            @error('warehouse_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Invoice #') }}</label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number', $invoiceNumber) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            @error('invoice_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </x-admin.card>

                <x-admin.card :title="__('Items')">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="px-2 py-2 text-start w-1/3">{{ __('Product') }}</th>
                                    <th class="px-2 py-2 text-start">{{ __('Variant') }}</th>
                                    <th class="px-2 py-2 text-start w-24">{{ __('Qty') }}</th>
                                    <th class="px-2 py-2 text-start w-32">{{ __('Unit Cost') }}</th>
                                    <th class="px-2 py-2 text-start w-32">{{ __('Total') }}</th>
                                    <th class="px-2 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in items" :key="row.key">
                                    <tr class="border-t border-slate-100 align-top">
                                        <td class="px-2 py-2">
                                            <select class="product-search w-full" :data-row-index="index" x-init="initProductSelect($el, row)"></select>
                                            <input type="hidden" :name="'items['+index+'][product_id]'" :value="row.product_id">
                                            <p class="text-[11px] text-slate-400 mt-1" x-show="row.sku" x-text="row.sku"></p>
                                        </td>
                                        <td class="px-2 py-2">
                                            <select x-show="row.has_variants" x-model="row.variant_id" :name="row.has_variants ? ('items['+index+'][variant_id]') : ''" class="w-full rounded-lg border-slate-300 text-xs">
                                                <option value="">{{ __('Select variant') }}</option>
                                                <template x-for="variant in row.variants" :key="variant.id">
                                                    <option :value="variant.id" x-text="variant.sku" @click="row.unit_cost = variant.cost_price || row.unit_cost"></option>
                                                </template>
                                            </select>
                                            <span x-show="!row.has_variants" class="text-slate-300 text-xs">—</span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" min="1" step="1" :name="'items['+index+'][qty]'" x-model.number="row.qty" class="w-20 rounded-lg border-slate-300 text-xs">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" min="0" step="0.01" :name="'items['+index+'][unit_cost]'" x-model.number="row.unit_cost" class="w-28 rounded-lg border-slate-300 text-xs">
                                        </td>
                                        <td class="px-2 py-2 text-sm font-medium text-slate-700" x-text="rowTotal(row).toFixed(2)"></td>
                                        <td class="px-2 py-2 text-end">
                                            <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-red-600">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!items.length">
                                    <td colspan="6" class="px-2 py-6 text-center text-slate-400">{{ __('No items yet — search and add a product.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" @click="addItem()" class="mt-4 px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add Item') }}
                    </button>
                    @error('items')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
                </x-admin.card>

                <x-admin.card :title="__('Notes')">
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('notes') }}</textarea>
                </x-admin.card>
            </div>

            <div class="space-y-6">
                <x-admin.card :title="__('Totals')">
                    <div class="space-y-3">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Discount Amount') }}</label>
                            <input type="number" min="0" step="0.01" name="discount_amount" x-model.number="discountAmount" value="{{ old('discount_amount', 0) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tax Amount') }}</label>
                            <input type="number" min="0" step="0.01" name="tax_amount" x-model.number="taxAmount" value="{{ old('tax_amount', 0) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>

                        <div class="border-t border-slate-100 pt-3 space-y-1 text-sm">
                            <div class="flex justify-between text-slate-500">
                                <span>{{ __('Subtotal') }}</span>
                                <span x-text="subtotal().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>{{ __('Discount') }}</span>
                                <span x-text="'- ' + (discountAmount || 0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>{{ __('Tax') }}</span>
                                <span x-text="'+ ' + (taxAmount || 0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-base font-semibold text-slate-800 pt-2 border-t border-slate-100">
                                <span>{{ __('Grand Total') }}</span>
                                <span x-text="grandTotal().toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <div class="space-y-2">
                    <button type="submit" name="status" value="draft" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        {{ __('Save as Draft') }}
                    </button>
                    <button type="submit" name="status" value="confirmed" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700"
                        onclick="return confirm('{{ __('Confirming will update stock and post an accounting entry. Continue?') }}')">
                        {{ __('Confirm Purchase') }}
                    </button>
                    <a href="{{ route('admin.purchases.index') }}" class="block text-center px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>

    <x-admin.modal id="quick-add-supplier" :header="__('New Supplier')">
        <form id="quick-supplier-form" class="space-y-3">
            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Phone') }}</label>
                <input type="text" name="phone" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Email') }}</label>
                <input type="email" name="email" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Address') }}</label>
                <textarea name="address" rows="2" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
            </div>
        </form>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal-quick-add-supplier')" class="px-4 py-2 text-sm rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">
                {{ __('Cancel') }}
            </button>
            <button type="button" onclick="submitQuickSupplier()" class="px-4 py-2 text-sm rounded-lg bg-amber-600 text-white hover:bg-amber-700">
                {{ __('Save Supplier') }}
            </button>
        </x-slot:footer>
    </x-admin.modal>
@endsection

@push('scripts')
    <script>
        function purchaseForm({ searchUrl }) {
            return {
                supplierId: @js(old('supplier_id')),
                discountAmount: {{ (float) old('discount_amount', 0) }},
                taxAmount: {{ (float) old('tax_amount', 0) }},
                items: [],
                searchUrl,

                init() {
                    if (!this.items.length) this.addItem();
                },

                addItem() {
                    this.items.push({
                        key: Date.now() + Math.random(),
                        product_id: '',
                        variant_id: '',
                        sku: '',
                        has_variants: false,
                        variants: [],
                        qty: 1,
                        unit_cost: 0,
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                rowTotal(row) {
                    return (Number(row.qty) || 0) * (Number(row.unit_cost) || 0);
                },

                subtotal() {
                    return this.items.reduce((sum, row) => sum + this.rowTotal(row), 0);
                },

                grandTotal() {
                    const total = this.subtotal() - (Number(this.discountAmount) || 0) + (Number(this.taxAmount) || 0);
                    return Math.max(0, total);
                },

                initProductSelect(el, row) {
                    $(el).select2({
                        width: '100%',
                        placeholder: @js(__('Search product or SKU')),
                        minimumInputLength: 2,
                        ajax: {
                            url: this.searchUrl,
                            dataType: 'json',
                            delay: 250,
                            data: (params) => ({ q: params.term }),
                            processResults: (data) => ({ results: data }),
                        },
                        templateResult: (product) => product.text || product.loading,
                        templateSelection: (product) => product.text || row.sku,
                    }).on('select2:select', (e) => {
                        const data = e.params.data;
                        row.product_id = data.id;
                        row.sku = data.sku;
                        row.has_variants = data.has_variants;
                        row.variants = data.variants || [];
                        row.unit_cost = data.cost_price;
                        row.variant_id = row.has_variants && row.variants.length === 1 ? row.variants[0].id : '';
                    });
                },
            };
        }

        async function submitQuickSupplier() {
            const form = document.getElementById('quick-supplier-form');
            const formData = new FormData(form);

            const response = await fetch(@js(route('admin.purchases.quick-add-supplier')), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    Accept: 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.message || @js(__('Could not save supplier.')));
                return;
            }

            const option = new Option(data.text, data.id, true, true);
            $('#supplier-select').append(option).trigger('change');
            window.dispatchEvent(new CustomEvent('close-modal-quick-add-supplier'));
            form.reset();
        }
    </script>
@endpush
