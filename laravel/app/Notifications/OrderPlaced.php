<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('customer', 'order_placed');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Order Placed - :number', ['number' => $this->order->order_number]))
            ->view('emails.order-placed', ['order' => $this->order]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Order Placed'),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }
}
