<?php

declare(strict_types=1);

namespace Modules\Inventory\Observers;

use Modules\Inventory\Models\StockBalance;
use Modules\Inventory\Models\StockMovement;

class StockMovementObserver
{
    public function created(StockMovement $movement): void
    {
        $balance = StockBalance::query()->firstOrNew([
            'product_id' => $movement->product_id,
            'product_variant_id' => $movement->product_variant_id,
            'stock_location_id' => $movement->stock_location_id,
        ]);

        if (! $balance->exists) {
            $balance->quantity = 0;
            $balance->reserved_quantity = 0;
            $balance->save();
        }

        $balance->increment('quantity', $movement->quantity);
    }
}
