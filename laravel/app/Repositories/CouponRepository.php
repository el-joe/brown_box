<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    public function __construct(Coupon $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): ?Model
    {
        return $this->model->newQuery()->where('code', $code)->first();
    }

    public function incrementUsage(int $couponId): Model
    {
        $coupon = $this->findOrFail($couponId);
        $coupon->increment('used_count');

        return $coupon->refresh();
    }
}
