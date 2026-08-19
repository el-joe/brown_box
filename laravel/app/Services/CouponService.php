<?php

namespace App\Services;

use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CouponService
{
    public function __construct(private readonly CouponRepositoryInterface $coupons)
    {
    }

    public function find(int $id): ?Model
    {
        return $this->coupons->find($id);
    }

    public function findByCode(string $code): ?Model
    {
        return $this->coupons->findByCode($code);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $coupon = $this->coupons->create($this->baseAttributes($data));
            $this->syncRestrictions($coupon, $data);

            return $coupon->fresh(['products', 'categories']);
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $coupon = $this->coupons->update($id, $this->baseAttributes($data));
            $this->syncRestrictions($coupon, $data);

            return $coupon->fresh(['products', 'categories']);
        });
    }

    public function delete(int $id): bool
    {
        return $this->coupons->delete($id);
    }

    private function baseAttributes(array $data): array
    {
        return [
            'code' => $data['code'],
            'type' => $data['type'],
            'value' => $data['type'] === 'free_shipping' ? 0 : $data['value'],
            'min_order_amount' => $data['min_order_amount'] ?? null,
            'max_uses' => $data['max_uses'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ];
    }

    private function syncRestrictions(Model $coupon, array $data): void
    {
        $applyTo = $data['apply_to'] ?? 'all';

        $coupon->products()->sync($applyTo === 'specific_products' ? ($data['product_ids'] ?? []) : []);
        $coupon->categories()->sync($applyTo === 'specific_categories' ? ($data['category_ids'] ?? []) : []);
    }

    /**
     * Validate a coupon against an order subtotal and, optionally, the cart's
     * items (each with product_id and category_id) to enforce product/category
     * restrictions. Throws if invalid.
     */
    public function validate(string $code, float $subtotal, array $cartItems = []): Model
    {
        $coupon = $this->coupons->findByCode($code);

        if (! $coupon) {
            throw new InvalidArgumentException(__('Coupon not found.'));
        }

        if (! $coupon->is_active) {
            throw new InvalidArgumentException(__('Coupon is not active.'));
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            throw new InvalidArgumentException(__('Coupon is not yet valid.'));
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw new InvalidArgumentException(__('Coupon has expired.'));
        }

        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            throw new InvalidArgumentException(__('Coupon usage limit reached.'));
        }

        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            throw new InvalidArgumentException(__('Order does not meet the coupon minimum amount.'));
        }

        if ($cartItems && ! $this->matchesRestrictions($coupon, $cartItems)) {
            throw new InvalidArgumentException(__('Coupon does not apply to the items in your cart.'));
        }

        return $coupon;
    }

    /**
     * Whether the coupon's product/category restrictions (if any) are
     * satisfied by at least one item in the cart.
     */
    private function matchesRestrictions(Model $coupon, array $cartItems): bool
    {
        $productIds = $coupon->products()->pluck('products.id');
        $categoryIds = $coupon->categories()->pluck('categories.id');

        if ($productIds->isEmpty() && $categoryIds->isEmpty()) {
            return true;
        }

        foreach ($cartItems as $item) {
            if ($productIds->isNotEmpty() && in_array($item['product_id'] ?? null, $productIds->all(), true)) {
                return true;
            }

            if ($categoryIds->isNotEmpty() && in_array($item['category_id'] ?? null, $categoryIds->all(), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compute the discount amount for a coupon applied to a subtotal.
     * Discount is capped at the subtotal.
     */
    public function calculateDiscount(Model $coupon, float $subtotal): float
    {
        $discount = $coupon->type === 'percentage'
            ? $subtotal * ((float) $coupon->value / 100)
            : (float) $coupon->value;

        return round(min($discount, $subtotal), 2);
    }

    public function markUsed(int $couponId): Model
    {
        return $this->coupons->incrementUsage($couponId);
    }
}
