<?php

namespace App\Enums;

enum UserType: string
{
    case Admin = 'admin';
    case Affiliate = 'affiliate';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Admin'),
            self::Affiliate => __('Affiliate'),
            self::Customer => __('Customer'),
        };
    }
}
