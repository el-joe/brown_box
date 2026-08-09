<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Notifications\LowStockNotification;
use App\Services\StockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendLowStockDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 600];

    public function handle(StockService $stocks): void
    {
        $lowStocks = $stocks->lowStock()->load(['product', 'variant', 'warehouse']);

        if ($lowStocks->isEmpty()) {
            return;
        }

        $admins = Admin::query()->active()->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new LowStockNotification($lowStocks));
        }
    }
}
