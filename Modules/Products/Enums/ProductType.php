<?php

declare(strict_types=1);

namespace Modules\Products\Enums;

enum ProductType: string
{
    case Simple = 'simple';
    case Variable = 'variable';

    public function label(): string
    {
        return match ($this) {
            self::Simple => 'Simples',
            self::Variable => 'Variável',
        };
    }
}
