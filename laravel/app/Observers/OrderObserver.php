<?php

namespace App\Observers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Jobs\GenerateInvoice;
use App\Models\Order;

class OrderObserver
{
    public function created(Order $order): void
    {
        event(new OrderPlaced($order));
        GenerateInvoice::dispatch($order)->delay(now()->addSeconds(5));
    }

    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status')?->value;
            $newStatus = $order->status->value;

            event(new OrderStatusChanged($order, $oldStatus, $newStatus));
        }
    }
}
