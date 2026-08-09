<?php

namespace App\Repositories;

use App\Models\Affiliate;
use App\Repositories\Contracts\AffiliateRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class AffiliateRepository extends BaseRepository implements AffiliateRepositoryInterface
{
    public function __construct(Affiliate $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): ?Model
    {
        return $this->model->newQuery()->where('code', $code)->first();
    }

    public function findByCustomerId(int $customerId): ?Model
    {
        return $this->model->newQuery()->where('user_id', $customerId)->first();
    }

    public function incrementBalance(int $affiliateId, float $amount): Model
    {
        $affiliate = $this->findOrFail($affiliateId);
        $affiliate->increment('balance', $amount);
        $affiliate->increment('total_earned', $amount);

        return $affiliate->refresh();
    }

    public function decrementBalance(int $affiliateId, float $amount): Model
    {
        $affiliate = $this->findOrFail($affiliateId);
        $affiliate->decrement('balance', $amount);

        return $affiliate->refresh();
    }
}
