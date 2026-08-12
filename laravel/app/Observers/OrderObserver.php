<?php

namespace App\Observers;

use App\Events\OrderCreated;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Jobs\GenerateInvoice;
use App\Models\AccountingEntry;
use App\Models\Order;
use App\Models\OrderStatusHistory;

class OrderObserver
{
    public function created(Order $order): void
    {
        event(new OrderCreated($order));
        event(new OrderPlaced($order));

        AccountingEntry::create([
            'type' => 'credit',
            'category' => 'sales',
            'amount' => $order->total_amount,
            'description' => 'Order '.$order->order_number,
            'reference_type' => Order::class,
            'reference_id' => $order->id,
            'date' => now()->toDateString(),
        ]);

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
