<?php

namespace App\Enums;

enum CouponType: string
{
    case FreeShipping = 'free_shipping';
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::FreeShipping => 'Free Shipping',
            self::Percentage => 'Percentage',
            self::Fixed => 'Fixed',
        };
    }
}
