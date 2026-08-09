<?php

namespace App\Enums;

enum CommissionTierType: string
{
    case FixedPercentage = 'fixed_percentage';
    case Tiered = 'tiered';

    public function label(): string
    {
        return match ($this) {
            self::FixedPercentage => 'Fixed Percentage',
            self::Tiered => 'Tiered',
        };
    }
}
