<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendCustomerNotification;
use App\Notifications\OrderPlaced as OrderPlacedNotification;

class SendOrderPlacedNotification
{
    public function handle(OrderPlaced $event): void
    {
        $customer = $event->order->customer;

        if ($customer) {
            SendCustomerNotification::dispatch($customer, new OrderPlacedNotification($event->order));
        }
    }
}
