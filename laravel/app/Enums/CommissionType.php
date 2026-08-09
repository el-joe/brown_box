<?php

namespace App\Enums;

enum CommissionType: string
{
    case FixedAllOrders = 'fixed_all_orders';
    case PerCategory = 'per_category';

    public function label(): string
    {
        return match ($this) {
            self::FixedAllOrders => 'Fixed for All Orders',
            self::PerCategory => 'Per Category',
        };
    }
}
