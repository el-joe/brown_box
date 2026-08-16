<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        ['items' => $items, 'subtotal' => $subtotal] = $this->resolveCart();

        $cartProductIds = collect($items)->pluck('product.id')->all();

        return view('website.cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'relatedProducts' => Product::query()
                ->active()
                ->whereNotIn('id', $cartProductIds)
                ->with(['productImages', 'category'])
                ->latest()
                ->take(12)
                ->get(),
        ]);
    }

    private function resolveCart(): array
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

        return ['items' => $items, 'subtotal' => $subtotal];
    }

    public function add(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $qty = $data['qty'] ?? 1;
        $key = $data['product_id'].'_'.($data['variant_id'] ?? 0);

        $cart = session('cart', []);
        $cart[$key] = [
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null,
            'qty' => ($cart[$key]['qty'] ?? 0) + $qty,
        ];

        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('website.added_to_cart'),
                'cart_count' => collect($cart)->sum('qty'),
            ]);
        }

        return back()->with('success', __('website.added_to_cart'));
    }

    public function update(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $key = (string) $request->route('key');

        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session('cart', []);

        if (isset($cart[$key])) {
            $cart[$key]['qty'] = $data['qty'];
            session(['cart' => $cart]);
        }

        if ($request->wantsJson()) {
            ['items' => $items, 'subtotal' => $subtotal] = $this->resolveCart();

            return response()->json([
                'success' => true,
                'message' => __('website.cart_updated'),
                'cart_count' => collect($cart)->sum('qty'),
                'subtotal' => $subtotal,
                'line_total' => collect($items)->firstWhere('key', $key)['total'] ?? 0,
            ]);
        }

        return back()->with('success', __('website.cart_updated'));
    }

    public function remove(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $key = (string) $request->route('key');

        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            ['subtotal' => $subtotal] = $this->resolveCart();

            return response()->json([
                'success' => true,
                'message' => __('website.removed_from_cart'),
                'cart_count' => collect($cart)->sum('qty'),
                'subtotal' => $subtotal,
            ]);
        }

        return back()->with('success', __('website.removed_from_cart'));
    }

    public function clear(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        session()->forget('cart');

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('website.removed_from_cart'));
    }
}
