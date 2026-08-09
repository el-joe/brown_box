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
            self::BankTransfer => 'Bank Transfer',
            self::VodafoneCash => 'Vodafone Cash',
            self::Instapay => 'Instapay',
            self::Paymob => 'Paymob',
        };
    }
}
