<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case PendingVerification = 'pending_verification';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => __('Unpaid'),
            self::PendingVerification => __('Pending Verification'),
            self::Paid => __('Paid'),
            self::Failed => __('Failed'),
            self::Refunded => __('Refunded'),
        };
    }
}
