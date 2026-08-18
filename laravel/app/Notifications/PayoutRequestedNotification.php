<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly PayoutRequest $payout)
    {
    }

    public function via(object $notifiable): array
    {
        return app(\App\Services\NotificationChannelService::class)
            ->channels('admin', 'payout_request');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $affiliate = $this->payout->affiliate;

        return (new MailMessage())
            ->subject(__('New Affiliate Payout Request'))
            ->line(__('Affiliate :name requested a payout of :amount.', [
                'name' => $affiliate->customer?->name ?? $affiliate->code,
                'amount' => money_format((float) $this->payout->amount),
            ]))
            ->action(__('Review Payout Requests'), route('admin.affiliates.payouts.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('New Affiliate Payout Request'),
            'payout_request_id' => $this->payout->id,
            'affiliate_id' => $this->payout->affiliate_id,
            'amount' => (float) $this->payout->amount,
        ];
    }
}
