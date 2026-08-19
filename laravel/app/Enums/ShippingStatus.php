<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case Pending = 'pending';
    case PickedUp = 'picked_up';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::PickedUp => __('Picked Up'),
            self::InTransit => __('In Transit'),
            self::Delivered => __('Delivered'),
            self::Returned => __('Returned'),
        };
    }
}
