<?php

declare(strict_types=1);

namespace Modules\Purchases\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\StockBalance;
use Modules\Purchases\Actions\ReceivePurchaseOrderAction;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Tests\TestCase;

class ReceivePurchaseOrderActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiving_a_sent_order_creates_stock_movements_and_updates_the_balance(): void
    {
        $order = PurchaseOrder::factory()->sent()->create();
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'quantity' => 15,
        ]);

        $performer = User::factory()->create();

        $updated = (new ReceivePurchaseOrderAction)->execute($order, $performer->id);

        $this->assertSame(PurchaseOrderStatus::Received, $updated->status);
        $this->assertNotNull($updated->received_at);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $item->product_id,
            'stock_location_id' => $order->stock_location_id,
            'quantity' => 15,
            'performed_by' => $performer->id,
        ]);

        $balance = StockBalance::query()
            ->where('product_id', $item->product_id)
            ->where('stock_location_id', $order->stock_location_id)
            ->first();

        $this->assertSame(15, $balance->quantity);
    }

    public function test_a_draft_order_cannot_be_received(): void
    {
        $order = PurchaseOrder::factory()->create();
        $performer = User::factory()->create();

        $this->expectException(ValidationException::class);

        (new ReceivePurchaseOrderAction)->execute($order, $performer->id);
    }

    public function test_an_already_received_order_cannot_be_received_again(): void
    {
        $order = PurchaseOrder::factory()->received()->create();
        $performer = User::factory()->create();

        $this->expectException(ValidationException::class);

        (new ReceivePurchaseOrderAction)->execute($order, $performer->id);
    }
}
