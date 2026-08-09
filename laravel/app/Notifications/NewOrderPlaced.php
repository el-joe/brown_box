<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('New Order Received - :number', ['number' => $this->order->order_number]))
            ->view('emails.new-order-placed', ['order' => $this->order])
            ->action(__('View Order'), route('admin.orders.show', $this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('New Order Received'),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }
}
