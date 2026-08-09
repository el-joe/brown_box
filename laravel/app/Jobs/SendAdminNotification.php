<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendAdminNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  mixed  $notifiable  A single notifiable model or an iterable/Collection of them.
     */
    public function __construct(
        public readonly mixed $notifiable,
        public readonly LaravelNotification $notification,
    ) {
    }

    public function handle(): void
    {
        if ($this->notifiable instanceof \Illuminate\Support\Collection || is_iterable($this->notifiable) && ! $this->notifiable instanceof \Illuminate\Database\Eloquent\Model) {
            Notification::send($this->notifiable, $this->notification);

            return;
        }

        $this->notifiable->notify($this->notification);
    }
}
