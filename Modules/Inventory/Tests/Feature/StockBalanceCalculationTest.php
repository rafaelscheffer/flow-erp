<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockBalance;
use Modules\Inventory\Models\StockLocation;
use Modules\Inventory\Models\StockMovement;
use Modules\Products\Models\Product;
use Tests\TestCase;

class StockBalanceCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_entrada_movement_increases_the_stock_balance(): void
    {
        $product = Product::factory()->create();
        $location = StockLocation::factory()->create();

        StockMovement::factory()->create([
            'type' => StockMovementType::Entrada,
            'product_id' => $product->id,
            'product_variant_id' => null,
            'stock_location_id' => $location->id,
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('stock_balances', [
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => 10,
        ]);
    }

    public function test_a_saida_movement_decreases_the_stock_balance(): void
    {
        $product = Product::factory()->create();
        $location = StockLocation::factory()->create();

        StockMovement::factory()->create([
            'type' => StockMovementType::Entrada,
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => 10,
        ]);

        StockMovement::factory()->create([
            'type' => StockMovementType::Saida,
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => -4,
        ]);

        $balance = StockBalance::query()
            ->where('product_id', $product->id)
            ->where('stock_location_id', $location->id)
            ->first();

        $this->assertSame(6, $balance->quantity);
    }
}
