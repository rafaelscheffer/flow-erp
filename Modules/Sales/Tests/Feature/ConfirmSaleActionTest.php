<?php

declare(strict_types=1);

namespace Modules\Sales\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\StockBalance;
use Modules\Sales\Actions\ConfirmSaleAction;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Events\OrderConfirmed;
use Modules\Sales\Models\Order;
use Modules\Sales\Models\OrderItem;
use Tests\TestCase;

class ConfirmSaleActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirming_a_draft_order_creates_stock_movements_updates_the_balance_and_dispatches_an_event(): void
    {
        Event::fake([OrderConfirmed::class]);

        $order = Order::factory()->create();
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
            'quantity' => 5,
        ]);

        $performer = User::factory()->create();

        $updated = (new ConfirmSaleAction)->execute($order, $performer->id);

        $this->assertSame(OrderStatus::Confirmed, $updated->status);
        $this->assertNotNull($updated->confirmed_at);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $item->product_id,
            'stock_location_id' => $order->stock_location_id,
            'quantity' => -5,
            'performed_by' => $performer->id,
        ]);

        $balance = StockBalance::query()
            ->where('product_id', $item->product_id)
            ->where('stock_location_id', $order->stock_location_id)
            ->first();

        $this->assertSame(-5, $balance->quantity);

        Event::assertDispatched(OrderConfirmed::class, fn (OrderConfirmed $event): bool => $event->order->is($order));
    }

    public function test_a_confirmed_order_cannot_be_confirmed_again(): void
    {
        $order = Order::factory()->confirmed()->create();
        $performer = User::factory()->create();

        $this->expectException(ValidationException::class);

        (new ConfirmSaleAction)->execute($order, $performer->id);
    }

    public function test_a_cancelled_order_cannot_be_confirmed(): void
    {
        $order = Order::factory()->cancelled()->create();
        $performer = User::factory()->create();

        $this->expectException(ValidationException::class);

        (new ConfirmSaleAction)->execute($order, $performer->id);
    }
}
