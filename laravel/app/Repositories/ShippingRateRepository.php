<?php

namespace App\Repositories;

use App\Models\ShippingRate;
use App\Repositories\Contracts\ShippingRateRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ShippingRateRepository extends BaseRepository implements ShippingRateRepositoryInterface
{
    public function __construct(ShippingRate $model)
    {
        parent::__construct($model);
    }

    public function findForLocation(int $shippingCompanyId, ?int $governorateId, ?int $cityId): ?Model
    {
        $query = $this->model->newQuery()->where('shipping_company_id', $shippingCompanyId);

        if ($cityId) {
            $rate = (clone $query)->where('city_id', $cityId)->first();

            if ($rate) {
                return $rate;
            }
        }

        if ($governorateId) {
            $rate = (clone $query)->where('governorate_id', $governorateId)->whereNull('city_id')->first();

            if ($rate) {
                return $rate;
            }
        }

        return null;
    }
}
