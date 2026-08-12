<?php

declare(strict_types=1);

namespace Modules\Purchases\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Sent => 'Enviado',
            self::Received => 'Recebido',
            self::Cancelled => 'Cancelado',
        };
    }
}
