<?php

namespace App\Repositories\Contracts;

interface AccountingEntryRepositoryInterface extends BaseRepositoryInterface
{
    public function totalByTypeBetween(string $type, ?string $from = null, ?string $to = null): float;
}
