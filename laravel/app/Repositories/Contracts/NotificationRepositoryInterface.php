<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function forNotifiable(string $notifiableType, int $notifiableId): Collection;

    public function markAsRead(string $id): void;
}
