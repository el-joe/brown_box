<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockLow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Collection $stocks)
    {
    }
}
