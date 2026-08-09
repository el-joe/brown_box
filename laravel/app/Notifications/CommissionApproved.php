<?php

namespace App\Notifications;

use App\Models\AffiliateCommission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommissionApproved extends Notification
{
    use Queueable;

    public function __construct(private readonly AffiliateCommission $commission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Commission Approved'))
            ->view('emails.commission-approved', ['commission' => $this->commission]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Commission Approved'),
            'commission_id' => $this->commission->id,
            'amount' => (float) $this->commission->amount,
            'order_id' => $this->commission->order_id,
        ];
    }
}
