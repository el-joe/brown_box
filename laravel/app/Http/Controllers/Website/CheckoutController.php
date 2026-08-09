<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Gateway;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $cart = session('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $line) {
            $product = Product::query()->find($line['product_id']);

            if (! $product) {
                continue;
            }

            $variant = $line['variant_id'] ? ProductVariant::query()->find($line['variant_id']) : null;
            $price = $variant ? (float) $variant->price : $product->effective_price;
            $lineTotal = $price * $line['qty'];
            $subtotal += $lineTotal;

            $items[] = [
                'key' => $key,
                'product' => $product,
                'variant' => $variant,
                'qty' => $line['qty'],
                'price' => $price,
                'total' => $lineTotal,
            ];
        }

        return view('website.checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'addresses' => Auth::guard('customer')->check()
                ? Auth::guard('customer')->user()->addresses()->with(['city', 'governorate'])->get()
                : collect(),
            'governorates' => Governorate::query()->with('cities')->orderBy('name_en')->get(),
            'gateways' => Gateway::query()->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['nullable', 'integer', 'exists:customer_addresses,id'],
            'name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'phone' => ['required_without:address_id', 'nullable', 'string', 'max:30'],
            'governorate_id' => ['required_without:address_id', 'nullable', 'integer', 'exists:governorates,id'],
            'city_id' => ['required_without:address_id', 'nullable', 'integer', 'exists:cities,id'],
            'address_line' => ['required_without:address_id', 'nullable', 'string', 'max:500'],
            'shipping_company_id' => ['nullable', 'integer', 'exists:shipping_companies,id'],
            'payment_gateway' => ['required', 'string'],
            'coupon_code' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = session('cart', []);

        abort_if(empty($cart), 422, 'Cart is empty.');

        /** @var ?Customer $customer */
        $customer = Auth::guard('customer')->user();

        // Resolve address either from a saved CustomerAddress or the inline form fields.
        if (! empty($data['address_id']) && $customer) {
            $address = $customer->addresses()->findOrFail($data['address_id']);
            $addressPayload = [
                'name' => $address->name,
                'phone' => $address->phone,
                'governorate_id' => $address->governorate_id,
                'city_id' => $address->city_id,
                'address_line' => $address->address_line,
            ];
        } else {
            $addressPayload = [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'governorate_id' => $data['governorate_id'],
                'city_id' => $data['city_id'],
                'address_line' => $data['address_line'],
            ];
        }

        $shippingAmount = 0;

        if (! empty($data['shipping_company_id'])) {
            $rate = ShippingRate::query()
                ->where('shipping_company_id', $data['shipping_company_id'])
                ->where('city_id', $addressPayload['city_id'])
                ->first();

            $shippingAmount = $rate ? (float) $rate->price : 0;
        }

        $order = DB::transaction(function () use ($cart, $data, $customer, $addressPayload, $shippingAmount) {
            $subtotal = 0;
            $lines = [];

            foreach ($cart as $line) {
                $product = Product::query()->findOrFail($line['product_id']);
                $variant = $line['variant_id'] ? ProductVariant::query()->find($line['variant_id']) : null;
                $price = $variant ? (float) $variant->price : $product->effective_price;
                $total = $price * $line['qty'];
                $subtotal += $total;

                $lines[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'qty' => $line['qty'],
                    'price' => $price,
                    'total' => $total,
                ];
            }

            $discount = 0;
            $coupon = null;

            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::query()->valid()->where('code', $data['coupon_code'])->first();

                if ($coupon) {
                    $discount = $coupon->type === 'percentage'
                        ? $subtotal * $coupon->value / 100
                        : (float) $coupon->value;
                }
            }

            $order = Order::query()->create([
                'order_number' => generate_order_number(),
                'customer_id' => $customer?->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_gateway' => $data['payment_gateway'],
                'coupon_id' => $coupon?->id,
                'coupon_discount' => $discount,
                'subtotal' => $subtotal,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discount,
                'tax_amount' => 0,
                'total_amount' => max(0, $subtotal - $discount + $shippingAmount),
                'shipping_company_id' => $data['shipping_company_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'customer_address' => $addressPayload,
            ]);

            foreach ($lines as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'variant_id' => $line['variant']?->id,
                    'product_name' => $line['product']->name,
                    'product_sku' => $line['variant']?->sku ?? $line['product']->sku,
                    'variant_label' => null,
                    'qty' => $line['qty'],
                    'unit_price' => $line['price'],
                    'discount_amount' => 0,
                    'total_price' => $line['total'],
                ]);
            }

            return $order;
        });

        session()->forget('cart');
        session(['last_order_number' => $order->order_number]);

        // For the Paymob gateway, real integration would redirect to the Paymob iframe here
        // using the order total and a token minted via Paymob's API (config in Gateway::config).
        // Left as a redirect to the success page with the order pending payment verification.

        return redirect()->route('web.checkout.success');
    }

    public function success(): View
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', session('last_order_number'))
            ->first();

        return view('website.checkout.success', [
            'order' => $order,
        ]);
    }
}
