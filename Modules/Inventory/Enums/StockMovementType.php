<?php

declare(strict_types=1);

namespace Modules\Inventory\Enums;

enum StockMovementType: string
{
    case Entrada = 'entrada';
    case Saida = 'saida';
    case Transferencia = 'transferencia';
    case Inventario = 'inventario';
    case Ajuste = 'ajuste';

    public function label(): string
    {
        return match ($this) {
            self::Entrada => 'Entrada',
            self::Saida => 'Saída',
            self::Transferencia => 'Transferência',
            self::Inventario => 'Inventário',
            self::Ajuste => 'Ajuste',
        };
    }

    /**
     * Types that can be created directly through the generic movement form.
     * Transferências are created through the dedicated transfer action, which
     * writes both legs (origin/destination) atomically.
     */
    public static function creatableCases(): array
    {
        return [self::Entrada, self::Saida, self::Inventario, self::Ajuste];
    }
}
