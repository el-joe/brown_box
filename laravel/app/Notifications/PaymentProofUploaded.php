<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentProofUploaded extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('admin', 'payment_proof');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Payment Proof Uploaded - :number', ['number' => $this->order->order_number]))
            ->view('emails.payment-proof-uploaded', ['order' => $this->order])
            ->action(__('Review Order'), route('admin.orders.show', $this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Payment Proof Uploaded'),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }
}
