<?php

namespace App\Events;

use App\Models\PayoutRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AffiliatePayoutApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly PayoutRequest $payoutRequest)
    {
    }
}
