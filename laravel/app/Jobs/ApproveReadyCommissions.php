<?php

namespace App\Jobs;

use App\Models\AffiliateCommission;
use App\Services\AffiliateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApproveReadyCommissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AffiliateService $affiliates): void
    {
        AffiliateCommission::query()
            ->where('status', 'pending')
            ->where('available_at', '<=', now())
            ->pluck('id')
            ->each(fn (int $commissionId) => $affiliates->approveCommission($commissionId));
    }
}
