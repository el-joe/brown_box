<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Models\AccountingEntry;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\AffiliateService;

class OrderObserver
{
    public function __construct(private readonly AffiliateService $affiliates) {}

    public function created(Order $order): void
    {
        event(new OrderCreated($order));

        AccountingEntry::create([
            'type' => 'order',
            'category' => 'sales',
            'amount' => $order->total_amount,
            'description' => 'Order '.$order->order_number,
            'reference_type' => Order::class,
            'reference_id' => $order->id,
            'date' => now()->toDateString(),
        ]);
    }

    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            event(new OrderStatusChanged($order, $oldStatus, $newStatus));

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'notes' => "Status changed from {$oldStatus} to {$newStatus}",
                'created_at' => now(),
            ]);

            if ($newStatus === OrderStatus::Delivered->value
                && $order->affiliate_id
                && ! $order->affiliateCommissions()->exists()
            ) {
                $this->affiliates->recordCommissionForOrder($order->affiliate, $order);
            }
        }
    }
}
