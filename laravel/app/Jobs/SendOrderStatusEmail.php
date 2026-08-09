<?php

namespace App\Jobs;

use App\Mail\OrderStatusMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly string $heading,
        public readonly string $message,
    ) {
    }

    public function handle(): void
    {
        $customer = $this->order->customer;

        if (! $customer?->email) {
            return;
        }

        Mail::to($customer->email)->send(new OrderStatusMail($this->order, $this->heading, $this->message));
    }
}
