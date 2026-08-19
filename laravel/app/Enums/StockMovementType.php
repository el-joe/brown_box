<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Adjustment = 'adjustment';
    case Return = 'return';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => __('Purchase'),
            self::Sale => __('Sale'),
            self::Adjustment => __('Adjustment'),
            self::Return => __('Return'),
        };
    }
}
