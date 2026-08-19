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
            self::FreeShipping => __('Free Shipping'),
            self::Percentage => __('Percentage'),
            self::Fixed => __('Fixed'),
        };
    }
}
