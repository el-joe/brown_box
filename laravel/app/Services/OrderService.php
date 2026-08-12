<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Jobs\GenerateInvoice;
use App\Jobs\SendCustomerNotification;
use App\Jobs\SendOrderStatusEmail;
use App\Notifications\OrderConfirmed;
use App\Notifications\OrderDelivered;
use App\Notifications\OrderRejected;
use App\Notifications\OrderShipped;
use App\Notifications\OrderStatusChanged as OrderStatusChangedNotification;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    private const CANCELLABLE_STATUSES = ['pending', 'processing'];

    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly StockService $stockService,
        private readonly AccountingService $accountingService,
        private readonly CouponService $couponService,
        private readonly AffiliateService $affiliateService,
        private readonly PaymentService $paymentService,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->orders->paginate($filters, $perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->orders->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Model
    {
        return $this->orders->findByOrderNumber($orderNumber);
    }

    /**
     * Create an order with its items, deduct stock, optionally apply a
     * coupon and affiliate commission, and record accounting income.
     *
     * $data must contain: customer_id, items (array of
     * [product_id, variant_id?, product_name, product_sku, qty, unit_price]),
     * shipping_company_id?, warehouse_id, customer_address, coupon_code?,
     * affiliate_code?, payment_gateway.
     */
    public function createOrder(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];

            if (empty($items)) {
                throw new RuntimeException('An order must have at least one item.');
            }

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += ($item['unit_price'] * $item['qty']) - ($item['discount_amount'] ?? 0);
            }

            $discountAmount = 0;
            $coupon = null;

            if (! empty($data['coupon_code'])) {
                $coupon = $this->couponService->validate($data['coupon_code'], $subtotal);
                $discountAmount = $this->couponService->calculateDiscount($coupon, $subtotal);
            }

            $shippingAmount = $data['shipping_amount'] ?? 0;
            $taxAmount = $data['tax_amount'] ?? 0;
            $totalAmount = $subtotal - $discountAmount + $shippingAmount + $taxAmount;

            $affiliateCode = $data['affiliate_code'] ?? session('affiliate_ref_code');

            $affiliate = null;
            if (! empty($affiliateCode)) {
                $affiliate = $this->affiliateService->findByCode($affiliateCode);
            }

            $order = $this->orders->create([
                'order_number' => $data['order_number'] ?? $this->generateOrderNumber(),
                'customer_id' => $data['customer_id'],
                'status' => 'pending',
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'payment_gateway' => $data['payment_gateway'] ?? null,
                'coupon_id' => $coupon?->id,
                'coupon_discount' => $discountAmount,
                'subtotal' => $subtotal,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'shipping_company_id' => $data['shipping_company_id'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'affiliate_id' => $affiliate?->id,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'variant_label' => $item['variant_label'] ?? null,
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_price' => ($item['unit_price'] * $item['qty']) - ($item['discount_amount'] ?? 0),
                    'product_snapshot' => $item['product_snapshot'] ?? null,
                ]);

                $this->stockService->deduct(
                    productId: $item['product_id'],
                    warehouseId: $data['warehouse_id'],
                    qty: $item['qty'],
                    variantId: $item['variant_id'] ?? null,
                    referenceType: 'order',
                    referenceId: $order->id,
                    note: "Order #{$order->order_number}",
                );
            }

            if ($coupon) {
                $this->couponService->markUsed($coupon->id);
            }

            if ($affiliate) {
                $this->affiliateService->recordCommissionForOrder($affiliate, $order);
            }

            $this->orders->addStatusHistory($order->id, 'pending', 'Order created.');

            $this->accountingService->recordIncome(
                category: 'sales',
                amount: (float) $totalAmount,
                description: "Order #{$order->order_number}",
                referenceType: 'order',
                referenceId: $order->id,
            );

            event(new OrderCreated($order));

            return $order->fresh(['items', 'customer']);
        });
    }

    public function changeStatus(int $orderId, string $status, ?string $notes = null, ?int $adminId = null): Model
    {
        return DB::transaction(function () use ($orderId, $status, $notes, $adminId) {
            $order = $this->orders->findOrFail($orderId);
            $previousStatus = $order->status->value;

            $timestamps = match ($status) {
                'shipped' => ['shipped_at' => now()],
                'delivered' => ['delivered_at' => now()],
                'cancelled' => ['cancelled_at' => now()],
                default => [],
            };

            $order->update(array_merge(['status' => $status], $timestamps));
            $this->orders->addStatusHistory($orderId, $status, $notes, $adminId);

            event(new OrderStatusChanged($order, $previousStatus, $status));

            $order = $order->fresh(['customer']);

            $notification = match ($status) {
                'confirmed' => new OrderConfirmed($order),
                'shipped' => new OrderShipped($order),
                'delivered' => new OrderDelivered($order),
                default => new OrderStatusChangedNotification($order, $status),
            };

            if ($order->customer) {
                SendCustomerNotification::dispatch($order->customer, $notification);
            } else {
                SendOrderStatusEmail::dispatch(
                    $order,
                    __('Order Status Updated'),
                    __('Your order status has changed to :status.', ['status' => $status]),
                );
            }

            if ($status === 'confirmed') {
                GenerateInvoice::dispatch($order);
            }

            return $order;
        });
    }

    /**
     * Approve a pending manual payment: marks payment as paid, order as
     * confirmed, records history, and emails the customer.
     */
    public function verifyPayment(int $orderId, ?int $adminId, ?string $notes = null): Model
    {
        return DB::transaction(function () use ($orderId, $adminId, $notes) {
            $this->paymentService->verifyManually($orderId, $adminId, true, $notes ?? 'Payment verified by admin.');

            return $this->changeStatus($orderId, 'confirmed', $notes ?? 'Payment verified, order confirmed.', $adminId);
        });
    }

    /**
     * Reject a pending manual payment and notify the customer, without
     * changing the order status.
     */
    public function rejectPayment(int $orderId, ?int $adminId, ?string $notes = null): Model
    {
        return DB::transaction(function () use ($orderId, $adminId, $notes) {
            $order = $this->paymentService->verifyManually($orderId, $adminId, false, $notes ?? 'Payment rejected by admin.');

            $order->loadMissing('customer');

            if ($order->customer) {
                SendCustomerNotification::dispatch($order->customer, new OrderRejected($order));
            } else {
                SendOrderStatusEmail::dispatch(
                    $order,
                    __('Payment Rejected'),
                    $notes ?? __('We were unable to verify your payment for this order. Please contact support.'),
                );
            }

            return $order;
        });
    }

    /**
     * Assign a shipping company and tracking number to the order.
     */
    public function assignShipping(int $orderId, ?int $shippingCompanyId, ?string $trackingNumber, ?int $adminId = null): Model
    {
        return DB::transaction(function () use ($orderId, $shippingCompanyId, $trackingNumber, $adminId) {
            $order = $this->orders->findOrFail($orderId);

            $order->update([
                'shipping_company_id' => $shippingCompanyId,
                'tracking_number' => $trackingNumber,
            ]);

            $this->orders->addStatusHistory($orderId, $order->status->value, 'Shipping company/tracking updated.', $adminId);

            return $order->fresh();
        });
    }

    /**
     * Update the shipping status, record history, and notify the customer.
     */
    public function changeShippingStatus(int $orderId, string $shippingStatus, ?int $adminId = null, ?string $notes = null): Model
    {
        return DB::transaction(function () use ($orderId, $shippingStatus, $adminId, $notes) {
            $order = $this->orders->findOrFail($orderId);

            $timestamps = match ($shippingStatus) {
                'delivered' => ['delivered_at' => now()],
                default => [],
            };

            $order->update(array_merge(['shipping_status' => $shippingStatus], $timestamps));
            $this->orders->addStatusHistory($orderId, $order->status->value, $notes ?? "Shipping status changed to {$shippingStatus}.", $adminId);

            $order = $order->fresh();

            SendOrderStatusEmail::dispatch(
                $order,
                __('Shipping Update'),
                __('Your order shipping status is now :status.', ['status' => $shippingStatus]),
            );

            return $order;
        });
    }

    public function updateAdminNotes(int $orderId, string $notes): Model
    {
        return $this->orders->update($orderId, ['admin_notes' => $notes]);
    }

    /**
     * Create an order from the POS screen. Marks payment as paid immediately
     * for cash/card and skips coupon validation errors gracefully.
     */
    public function createFromPOS(array $data): Model
    {
        $data['payment_status'] = $data['payment_status'] ?? 'paid';
        $data['customer_address'] = $data['customer_address'] ?? ['source' => 'pos'];

        $order = $this->createOrder($data);

        return $this->changeStatus($order->id, 'confirmed', 'Order placed via POS.', $data['admin_id'] ?? null);
    }

    /**
     * Cancel an order (only allowed from pending/processing) and restock items.
     */
    public function cancel(int $orderId, ?string $reason = null, ?int $adminId = null, ?int $warehouseId = null): Model
    {
        return DB::transaction(function () use ($orderId, $reason, $adminId, $warehouseId) {
            $order = $this->orders->findOrFail($orderId);

            if (! in_array($order->status->value, self::CANCELLABLE_STATUSES, true)) {
                throw new RuntimeException("Order in status '{$order->status->value}' cannot be cancelled.");
            }

            if ($warehouseId) {
                foreach ($order->items as $item) {
                    $this->stockService->add(
                        productId: $item->product_id,
                        warehouseId: $warehouseId,
                        qty: $item->qty,
                        variantId: $item->variant_id,
                        referenceType: 'order_cancellation',
                        referenceId: $order->id,
                        adminId: $adminId,
                        note: "Cancelled order #{$order->order_number}",
                    );
                }
            }

            return $this->changeStatus($orderId, 'cancelled', $reason ?? 'Order cancelled.', $adminId);
        });
    }

    private function generateOrderNumber(): string
    {
        return generate_order_number();
    }
}
