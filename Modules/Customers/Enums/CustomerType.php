<?php

declare(strict_types=1);

namespace Modules\Customers\Enums;

enum CustomerType: string
{
    case Individual = 'individual';
    case Company = 'company';

    public function label(): string
    {
        return match ($this) {
            self::Individual => 'Pessoa Física',
            self::Company => 'Pessoa Jurídica',
        };
    }

    public function documentLabel(): string
    {
        return match ($this) {
            self::Individual => 'CPF',
            self::Company => 'CNPJ',
        };
    }
}
