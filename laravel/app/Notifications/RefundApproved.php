<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundApproved extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly float $amount,
    ) {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('customer', 'refund_approved');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Refund Approved - :number', ['number' => $this->order->order_number]))
            ->view('emails.refund-approved', [
                'order' => $this->order,
                'amount' => $this->amount,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Refund Approved'),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->amount,
        ];
    }
}
