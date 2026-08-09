<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 600];

    public function __construct(
        public readonly Order $order,
        public readonly bool $emailToCustomer = false,
    ) {
    }

    public function handle(InvoiceService $invoices): void
    {
        $invoices->generate($this->order);

        if ($this->emailToCustomer && $this->order->customer?->email) {
            Mail::to($this->order->customer->email)->send(new InvoiceMail($this->order->fresh()));
        }
    }
}
