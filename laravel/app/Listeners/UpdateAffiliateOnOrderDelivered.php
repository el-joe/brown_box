<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\CommissionEarned;
use App\Events\OrderStatusChanged;
use App\Services\AffiliateService;

class UpdateAffiliateOnOrderDelivered
{
    public function __construct(private readonly AffiliateService $affiliates)
    {
    }

    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;

        if ($event->newStatus !== OrderStatus::Delivered->value
            || ! $order->affiliate_id
            || $order->affiliateCommissions()->exists()
        ) {
            return;
        }

        $commission = $this->affiliates->recordCommissionForOrder($order->affiliate, $order);

        event(new CommissionEarned($commission));
    }
}
