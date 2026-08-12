<?php

declare(strict_types=1);

namespace Modules\Purchases\Enums;

enum SupplierType: string
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
