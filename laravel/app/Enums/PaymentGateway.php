<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case BankTransfer = 'bank_transfer';
    case VodafoneCash = 'vodafone_cash';
    case Instapay = 'instapay';
    case Paymob = 'paymob';

    public function label(): string
    {
        return match ($this) {
            self::BankTransfer => __('Bank Transfer'),
            self::VodafoneCash => __('Vodafone Cash'),
            self::Instapay => __('Instapay'),
            self::Paymob => __('Paymob'),
        };
    }
}
