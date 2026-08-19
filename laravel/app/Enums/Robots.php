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
            self::IndexFollow => __('Index, Follow'),
            self::NoIndexFollow => __('No Index, Follow'),
            self::IndexNoFollow => __('Index, No Follow'),
            self::NoIndexNoFollow => __('No Index, No Follow'),
        };
    }
}
