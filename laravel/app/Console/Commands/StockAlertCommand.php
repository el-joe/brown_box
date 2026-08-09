<?php

namespace App\Console\Commands;

use App\Jobs\StockAlertCheck;
use Illuminate\Console\Command;

class StockAlertCommand extends Command
{
    protected $signature = 'stock:alert';

    protected $description = 'Check for low/out of stock products and notify admins';

    public function handle(): int
    {
        StockAlertCheck::dispatch();

        $this->info('Stock alert check dispatched.');

        return self::SUCCESS;
    }
}
