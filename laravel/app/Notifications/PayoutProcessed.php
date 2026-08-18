<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutProcessed extends Notification
{
    use Queueable;

    public function __construct(private readonly PayoutRequest $payout)
    {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('customer', 'payout');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Payout Processed'))
            ->view('emails.payout-processed', ['payout' => $this->payout]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Payout Processed'),
            'payout_request_id' => $this->payout->id,
            'amount' => (float) $this->payout->amount,
            'status' => $this->payout->status,
        ];
    }
}
