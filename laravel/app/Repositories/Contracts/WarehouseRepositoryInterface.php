<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WarehouseRepositoryInterface extends BaseRepositoryInterface
{
    public function default(): ?Model;
}
