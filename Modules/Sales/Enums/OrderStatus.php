<?php

declare(strict_types=1);

namespace Modules\Sales\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Invoiced = 'invoiced';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Confirmed => 'Confirmado',
            self::Invoiced => 'Faturado',
            self::Cancelled => 'Cancelado',
        };
    }
}
