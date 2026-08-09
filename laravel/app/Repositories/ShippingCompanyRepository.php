<?php

namespace App\Repositories;

use App\Models\ShippingCompany;
use App\Repositories\Contracts\ShippingCompanyRepositoryInterface;

class ShippingCompanyRepository extends BaseRepository implements ShippingCompanyRepositoryInterface
{
    public function __construct(ShippingCompany $model)
    {
        parent::__construct($model);
    }
}
