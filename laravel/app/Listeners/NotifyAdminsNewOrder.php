<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendAdminNotification;
use App\Models\Admin;
use App\Notifications\NewOrderPlaced;

class NotifyAdminsNewOrder
{
    public function handle(OrderPlaced $event): void
    {
        $admins = Admin::query()->active()->get()->filter(fn (Admin $admin) => $admin->can('orders.view'));

        if ($admins->isNotEmpty()) {
            SendAdminNotification::dispatch($admins, new NewOrderPlaced($event->order));
        }
    }
}
