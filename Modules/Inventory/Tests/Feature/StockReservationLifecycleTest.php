<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Enums\StockReservationStatus;
use Modules\Inventory\Models\StockBalance;
use Modules\Inventory\Models\StockReservation;
use Tests\TestCase;

class StockReservationLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_active_reservation_increases_reserved_quantity(): void
    {
        $reservation = StockReservation::factory()->create(['quantity' => 7]);

        $balance = StockBalance::query()
            ->where('product_id', $reservation->product_id)
            ->where('stock_location_id', $reservation->stock_location_id)
            ->first();

        $this->assertSame(7, $balance->reserved_quantity);
    }

    public function test_releasing_a_reservation_decreases_reserved_quantity(): void
    {
        $reservation = StockReservation::factory()->create(['quantity' => 7]);

        $reservation->update(['status' => StockReservationStatus::Released]);

        $balance = StockBalance::query()
            ->where('product_id', $reservation->product_id)
            ->where('stock_location_id', $reservation->stock_location_id)
            ->first();

        $this->assertSame(0, $balance->reserved_quantity);
    }
}
