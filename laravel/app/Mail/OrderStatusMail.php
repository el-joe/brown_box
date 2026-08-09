<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly string $heading,
        public readonly string $message,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('Order :number Update', ['number' => $this->order->order_number]))
            ->view('emails.order-status');
    }
}
