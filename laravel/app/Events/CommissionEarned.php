<?php

namespace App\Events;

use App\Models\AffiliateCommission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommissionEarned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly AffiliateCommission $commission)
    {
    }
}
