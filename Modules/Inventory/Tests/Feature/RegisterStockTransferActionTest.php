<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Actions\RegisterStockTransferAction;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockLocation;
use Modules\Products\Models\Product;
use Tests\TestCase;

class RegisterStockTransferActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_two_linked_movements_and_updates_both_balances(): void
    {
        $product = Product::factory()->create();
        $origin = StockLocation::factory()->create();
        $destination = StockLocation::factory()->create();
        $user = User::factory()->create();

        [$originMovement, $destinationMovement] = app(RegisterStockTransferAction::class)->execute(
            productId: $product->id,
            productVariantId: null,
            fromLocationId: $origin->id,
            toLocationId: $destination->id,
            quantity: 5,
            performedBy: $user->id,
        );

        $this->assertSame(StockMovementType::Transferencia, $originMovement->type);
        $this->assertSame(-5, $originMovement->quantity);
        $this->assertSame(5, $destinationMovement->quantity);
        $this->assertSame($originMovement->transfer_group_id, $destinationMovement->transfer_group_id);

        $this->assertDatabaseHas('stock_balances', [
            'product_id' => $product->id,
            'stock_location_id' => $origin->id,
            'quantity' => -5,
        ]);

        $this->assertDatabaseHas('stock_balances', [
            'product_id' => $product->id,
            'stock_location_id' => $destination->id,
            'quantity' => 5,
        ]);
    }

    public function test_it_rejects_a_transfer_to_the_same_location(): void
    {
        $product = Product::factory()->create();
        $location = StockLocation::factory()->create();
        $user = User::factory()->create();

        $this->expectException(ValidationException::class);

        app(RegisterStockTransferAction::class)->execute(
            productId: $product->id,
            productVariantId: null,
            fromLocationId: $location->id,
            toLocationId: $location->id,
            quantity: 5,
            performedBy: $user->id,
        );
    }
}
