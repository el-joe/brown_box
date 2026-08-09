<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Notifications\LowStockNotification;
use App\Services\StockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class StockAlertCommand extends Command
{
    protected $signature = 'stock:alert';

    protected $description = 'Notify admins about products that are low on stock or out of stock';

    public function handle(StockService $stocks): int
    {
        $lowStocks = $stocks->lowStock()->load(['product', 'variant', 'warehouse']);

        if ($lowStocks->isEmpty()) {
            $this->info('No low stock items found.');

            return self::SUCCESS;
        }

        $admins = Admin::query()->active()->get();

        if ($admins->isEmpty()) {
            $this->warn('No active admins to notify.');

            return self::SUCCESS;
        }

        Notification::send($admins, new LowStockNotification($lowStocks));

        $this->info("Notified {$admins->count()} admin(s) about {$lowStocks->count()} low stock item(s).");

        return self::SUCCESS;
    }
}
