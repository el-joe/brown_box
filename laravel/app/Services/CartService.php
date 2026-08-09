<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Session;

/**
 * Session based shopping cart. Cart is stored as an array keyed by a
 * composite "product_id:variant_id" line key.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(private readonly ProductRepositoryInterface $products)
    {
    }

    public function add(int $productId, int $qty = 1, ?int $variantId = null): array
    {
        $cart = $this->contents();
        $key = $this->lineKey($productId, $variantId);

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'qty' => $qty,
            ];
        }

        $this->save($cart);

        return $cart;
    }

    public function updateQty(int $productId, int $qty, ?int $variantId = null): array
    {
        $cart = $this->contents();
        $key = $this->lineKey($productId, $variantId);

        if (! isset($cart[$key])) {
            return $cart;
        }

        if ($qty <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['qty'] = $qty;
        }

        $this->save($cart);

        return $cart;
    }

    public function remove(int $productId, ?int $variantId = null): array
    {
        $cart = $this->contents();
        unset($cart[$this->lineKey($productId, $variantId)]);
        $this->save($cart);

        return $cart;
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function contents(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Resolve cart lines into products and compute totals.
     */
    public function summary(): array
    {
        $lines = [];
        $subtotal = 0.0;

        foreach ($this->contents() as $line) {
            $product = $this->products->find($line['product_id']);

            if (! $product || ! $product->is_active) {
                continue;
            }

            $unitPrice = (float) $product->effective_price;
            $lineTotal = $unitPrice * $line['qty'];
            $subtotal += $lineTotal;

            $lines[] = [
                'product' => $product,
                'variant_id' => $line['variant_id'],
                'qty' => $line['qty'],
                'unit_price' => $unitPrice,
                'total' => $lineTotal,
            ];
        }

        return [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'count' => array_sum(array_column($this->contents(), 'qty')),
        ];
    }

    private function lineKey(int $productId, ?int $variantId): string
    {
        return $productId.':'.($variantId ?? 0);
    }

    private function save(array $cart): void
    {
        Session::put(self::SESSION_KEY, $cart);
    }
}
