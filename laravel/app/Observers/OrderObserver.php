<?php

namespace App\Observers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Jobs\GenerateInvoice;
use App\Models\Order;
use App\Models\OrderStatusHistory;

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

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'notes' => "Status changed from {$oldStatus} to {$newStatus}",
                'created_at' => now(),
            ]);
        }
    }
}
