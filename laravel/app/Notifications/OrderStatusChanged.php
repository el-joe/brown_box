<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly string $newStatus,
    ) {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('customer', 'order_status');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Order :number Update', ['number' => $this->order->order_number]))
            ->view('emails.order-status', [
                'order' => $this->order,
                'heading' => __('Order Status Updated'),
                'message' => __('Your order status has changed to :status.', ['status' => $this->newStatus]),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Order Status Updated'),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->newStatus,
        ];
    }
}
