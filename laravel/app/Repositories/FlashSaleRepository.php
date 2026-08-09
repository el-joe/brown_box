<?php

namespace App\Repositories;

use App\Models\FlashSale;
use App\Repositories\Contracts\FlashSaleRepositoryInterface;

class FlashSaleRepository extends BaseRepository implements FlashSaleRepositoryInterface
{
    public function __construct(FlashSale $model)
    {
        parent::__construct($model);
    }
}
