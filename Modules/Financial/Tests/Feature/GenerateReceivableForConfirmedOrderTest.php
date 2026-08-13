<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Receivable;
use Modules\Sales\Actions\ConfirmSaleAction;
use Modules\Sales\Models\Order;
use Modules\Sales\Models\OrderItem;
use Tests\TestCase;

class GenerateReceivableForConfirmedOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirming_a_sale_automatically_generates_a_receivable(): void
    {
        $order = Order::factory()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'quantity' => 2,
            'unit_price' => 150,
        ]);

        $performer = User::factory()->create();

        $confirmed = (new ConfirmSaleAction)->execute($order, $performer->id);

        $receivable = Receivable::query()->where('order_id', $confirmed->id)->first();

        $this->assertNotNull($receivable);
        $this->assertSame($confirmed->customer_id, $receivable->customer_id);
        $this->assertEquals($confirmed->total, (float) $receivable->amount);
        $this->assertTrue($receivable->due_date->isSameDay($confirmed->confirmed_at->copy()->addDays(30)));
    }
}
