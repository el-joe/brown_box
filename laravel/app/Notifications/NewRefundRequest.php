<?php

namespace App\Notifications;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRefundRequest extends Notification
{
    use Queueable;

    public function __construct(private readonly RefundRequest $refundRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('admin', 'new_refund');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('New Refund Request - Order :number', ['number' => $this->refundRequest->order?->order_number]))
            ->view('emails.new-refund-request', ['refundRequest' => $this->refundRequest])
            ->action(__('Review Refund Request'), route('admin.refunds.show', $this->refundRequest->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('New Refund Request'),
            'refund_request_id' => $this->refundRequest->id,
            'order_id' => $this->refundRequest->order_id,
        ];
    }
}
