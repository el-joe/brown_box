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
            self::Admin => 'Admin',
            self::Affiliate => 'Affiliate',
            self::Customer => 'Customer',
        };
    }
}
