<?php

namespace App\Repositories;

use App\Models\PayoutRequest;
use App\Repositories\Contracts\PayoutRequestRepositoryInterface;

class PayoutRequestRepository extends BaseRepository implements PayoutRequestRepositoryInterface
{
    public function __construct(PayoutRequest $model)
    {
        parent::__construct($model);
    }
}
