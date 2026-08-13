<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Payable;
use Modules\Purchases\Actions\ReceivePurchaseOrderAction;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Tests\TestCase;

class GeneratePayableForReceivedPurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiving_a_purchase_order_automatically_generates_a_payable(): void
    {
        $order = PurchaseOrder::factory()->sent()->create();
        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'quantity' => 4,
            'unit_cost' => 100,
        ]);

        $performer = User::factory()->create();

        $received = (new ReceivePurchaseOrderAction)->execute($order, $performer->id);

        $payable = Payable::query()->where('purchase_order_id', $received->id)->first();

        $this->assertNotNull($payable);
        $this->assertSame($received->supplier_id, $payable->supplier_id);
        $this->assertEquals($received->total, (float) $payable->amount);
        $this->assertTrue($payable->due_date->isSameDay($received->received_at->copy()->addDays(30)));
    }
}
