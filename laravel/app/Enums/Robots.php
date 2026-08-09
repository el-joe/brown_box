<?php

namespace App\Enums;

enum Robots: string
{
    case IndexFollow = 'index,follow';
    case NoIndexFollow = 'noindex,follow';
    case IndexNoFollow = 'index,nofollow';
    case NoIndexNoFollow = 'noindex,nofollow';

    public function label(): string
    {
        return match ($this) {
            self::IndexFollow => 'Index, Follow',
            self::NoIndexFollow => 'No Index, Follow',
            self::IndexNoFollow => 'Index, No Follow',
            self::NoIndexNoFollow => 'No Index, No Follow',
        };
    }
}
