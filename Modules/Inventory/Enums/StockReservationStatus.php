<?php

declare(strict_types=1);

namespace Modules\Inventory\Enums;

enum StockReservationStatus: string
{
    case Active = 'active';
    case Released = 'released';
    case Fulfilled = 'fulfilled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativa',
            self::Released => 'Liberada',
            self::Fulfilled => 'Atendida',
        };
    }
}
