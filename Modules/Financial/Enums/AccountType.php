<?php

declare(strict_types=1);

namespace Modules\Financial\Enums;

enum AccountType: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Asset => 'Ativo',
            self::Liability => 'Passivo',
            self::Equity => 'Patrimônio Líquido',
            self::Revenue => 'Receita',
            self::Expense => 'Despesa',
        };
    }
}
