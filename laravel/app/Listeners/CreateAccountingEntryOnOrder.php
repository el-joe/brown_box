<?php

namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Services\AccountingService;

class CreateAccountingEntryOnOrder
{
    public function __construct(private readonly AccountingService $accounting)
    {
    }

    public function handle(PaymentVerified $event): void
    {
        $this->accounting->recordSale($event->order);
    }
}
