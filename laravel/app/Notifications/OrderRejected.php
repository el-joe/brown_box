<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderRejected extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('customer', 'order_cancelled');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Order Rejected - :number', ['number' => $this->order->order_number]))
            ->view('emails.order-rejected', ['order' => $this->order]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Order Rejected'),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }
}
