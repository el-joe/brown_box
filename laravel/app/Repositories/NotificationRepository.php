<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function forNotifiable(string $notifiableType, int $notifiableId): Collection
    {
        return $this->model->newQuery()
            ->where('notifiable_type', $notifiableType)
            ->where('notifiable_id', $notifiableId)
            ->latest()
            ->get();
    }

    public function markAsRead(string $id): void
    {
        $this->model->newQuery()->whereKey($id)->update(['read_at' => now()]);
    }
}
