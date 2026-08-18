<?php

namespace App\Providers;

use App\Events\CommissionEarned;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Events\PaymentVerified;
use App\Events\StockLow;
use App\Listeners\CreateAccountingEntryOnOrder;
use App\Listeners\DeductStockOnOrderConfirmed;
use App\Listeners\NotifyAdminsNewOrder;
use App\Listeners\SendLowStockAlerts;
use App\Listeners\SendOrderPlacedNotification;
use App\Listeners\SendOrderStatusNotification;
use App\Listeners\UpdateAffiliateOnOrderDelivered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            SendOrderPlacedNotification::class,
            NotifyAdminsNewOrder::class,
        ],
        OrderStatusChanged::class => [
            UpdateAffiliateOnOrderDelivered::class,
            SendOrderStatusNotification::class,
        ],
        PaymentVerified::class => [
            DeductStockOnOrderConfirmed::class,
            CreateAccountingEntryOnOrder::class,
        ],
        StockLow::class => [
            SendLowStockAlerts::class,
        ],
        CommissionEarned::class => [],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
