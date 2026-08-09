<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly RefundRequest $refundRequest)
    {
    }

    public function build(): self
    {
        return $this->subject(__('Refund Request Update - Order :number', ['number' => $this->refundRequest->order->order_number]))
            ->view('emails.refund-status');
    }
}
