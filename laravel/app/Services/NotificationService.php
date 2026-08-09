<?php

namespace App\Services;

use Illuminate\Contracts\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Thin wrapper around Laravel's notification system. Persistence for
 * "database" channel notifications is handled by Laravel's built-in
 * DatabaseNotification (App\Models\Notification extends it), so this
 * service does not duplicate a custom notifications table.
 */
class NotificationService
{
    public function send(Notifiable $notifiable, LaravelNotification $notification): void
    {
        $notifiable->notify($notification);
    }

    public function sendToMany(iterable $notifiables, LaravelNotification $notification): void
    {
        NotificationFacade::send($notifiables, $notification);
    }

    public function unread(Notifiable $notifiable): Collection
    {
        return $notifiable->unreadNotifications()->get();
    }

    public function markAllRead(Notifiable $notifiable): void
    {
        $notifiable->unreadNotifications->markAsRead();
    }

    public function markRead(Notifiable $notifiable, string $notificationId): void
    {
        $notifiable->notifications()->whereKey($notificationId)->update(['read_at' => now()]);
    }
}
