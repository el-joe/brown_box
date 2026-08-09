<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRejected extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly string $reason,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Refund Request Update - :number', ['number' => $this->order->order_number]))
            ->view('emails.refund-rejected', [
                'order' => $this->order,
                'reason' => $this->reason,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Refund Rejected'),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'reason' => $this->reason,
        ];
    }
}
