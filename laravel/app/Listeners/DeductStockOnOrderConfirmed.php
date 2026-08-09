<?php

namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Services\StockService;

class DeductStockOnOrderConfirmed
{
    public function __construct(private readonly StockService $stocks)
    {
    }

    public function handle(PaymentVerified $event): void
    {
        $this->stocks->deductForOrder($event->order);
    }
}
