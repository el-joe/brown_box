<?php

namespace App\Listeners;

use App\Events\StockLow;
use App\Jobs\SendAdminNotification;
use App\Models\Admin;
use App\Notifications\LowStockNotification;

class SendLowStockAlerts
{
    public function handle(StockLow $event): void
    {
        $admins = Admin::query()->active()->get();

        if ($admins->isNotEmpty()) {
            SendAdminNotification::dispatch($admins, new LowStockNotification($event->stocks));
        }
    }
}
