<?php

namespace App\Repositories;

use App\Models\RefundRequest;
use App\Repositories\Contracts\RefundRequestRepositoryInterface;

class RefundRequestRepository extends BaseRepository implements RefundRequestRepositoryInterface
{
    public function __construct(RefundRequest $model)
    {
        parent::__construct($model);
    }
}
