<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Collection $stocks)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject(__('Low Stock Alert'))
            ->line(__('The following :count product(s) are low on stock or out of stock:', ['count' => $this->stocks->count()]));

        foreach ($this->stocks->take(20) as $stock) {
            $name = $stock->product?->getTranslation('name', 'en') ?? __('Unknown product');
            $variant = $stock->variant?->sku ? " ({$stock->variant->sku})" : '';
            $warehouse = $stock->warehouse?->name ?? __('Unknown warehouse');

            $message->line("- {$name}{$variant} @ {$warehouse}: {$stock->qty} / {$stock->min_qty_alert}");
        }

        return $message->action(__('View Stock'), route('admin.stock.index', ['status' => 'low']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Low Stock Alert'),
            'count' => $this->stocks->count(),
            'stock_ids' => $this->stocks->pluck('id')->all(),
        ];
    }
}
