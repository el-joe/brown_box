<?php

namespace App\Jobs;

use App\Events\StockLow;
use App\Services\StockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StockAlertCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 600];

    public function handle(StockService $stocks): void
    {
        $lowStocks = $stocks->lowStock()->load(['product', 'variant', 'warehouse']);

        if ($lowStocks->isNotEmpty()) {
            event(new StockLow($lowStocks));
        }
    }
}
