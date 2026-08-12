<?php

declare(strict_types=1);

namespace Modules\Inventory\Observers;

use Modules\Inventory\Enums\StockReservationStatus;
use Modules\Inventory\Models\StockBalance;
use Modules\Inventory\Models\StockReservation;

class StockReservationObserver
{
    public function created(StockReservation $reservation): void
    {
        if ($reservation->status !== StockReservationStatus::Active) {
            return;
        }

        $this->balanceFor($reservation)->increment('reserved_quantity', $reservation->quantity);
    }

    public function updated(StockReservation $reservation): void
    {
        if (! $reservation->wasChanged('status')) {
            return;
        }

        $previousStatus = $reservation->getOriginal('status');
        $previousStatus = $previousStatus instanceof StockReservationStatus ? $previousStatus->value : $previousStatus;

        if ($previousStatus === StockReservationStatus::Active->value && $reservation->status !== StockReservationStatus::Active) {
            $this->balanceFor($reservation)->decrement('reserved_quantity', $reservation->quantity);
        }
    }

    private function balanceFor(StockReservation $reservation): StockBalance
    {
        $balance = StockBalance::query()->firstOrNew([
            'product_id' => $reservation->product_id,
            'product_variant_id' => $reservation->product_variant_id,
            'stock_location_id' => $reservation->stock_location_id,
        ]);

        if (! $balance->exists) {
            $balance->quantity = 0;
            $balance->reserved_quantity = 0;
            $balance->save();
        }

        return $balance;
    }
}
