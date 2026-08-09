<?php

namespace App\Jobs;

use App\Mail\RefundStatusMail;
use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessRefundNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 600];

    public function __construct(public readonly RefundRequest $refundRequest)
    {
    }

    public function handle(): void
    {
        $customer = $this->refundRequest->customer;

        if (! $customer?->email) {
            return;
        }

        Mail::to($customer->email)->send(new RefundStatusMail($this->refundRequest));
    }
}
