<?php

declare(strict_types=1);

namespace Modules\Financial\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case CreditCard = 'credit_card';
    case BankSlip = 'bank_slip';
    case Pix = 'pix';
    case BankTransfer = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Dinheiro',
            self::CreditCard => 'Cartão',
            self::BankSlip => 'Boleto',
            self::Pix => 'Pix',
            self::BankTransfer => 'Transferência',
        };
    }
}
