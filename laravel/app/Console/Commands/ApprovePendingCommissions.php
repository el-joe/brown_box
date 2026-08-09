<?php

namespace App\Console\Commands;

use App\Jobs\ApproveReadyCommissions;
use Illuminate\Console\Command;

class ApprovePendingCommissions extends Command
{
    protected $signature = 'affiliates:approve-commissions';

    protected $description = 'Approve affiliate commissions that have become due';

    public function handle(): int
    {
        ApproveReadyCommissions::dispatch();

        $this->info('Commission approval job dispatched.');

        return self::SUCCESS;
    }
}
