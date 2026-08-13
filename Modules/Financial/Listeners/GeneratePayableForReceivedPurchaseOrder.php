<?php

declare(strict_types=1);

namespace Modules\Financial\Listeners;

use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Models\Payable;
use Modules\Purchases\Events\PurchaseOrderReceived;

class GeneratePayableForReceivedPurchaseOrder
{
    public function handle(PurchaseOrderReceived $event): void
    {
        $purchaseOrder = $event->purchaseOrder;

        Payable::create([
            'supplier_id' => $purchaseOrder->supplier_id,
            'purchase_order_id' => $purchaseOrder->id,
            'amount' => $purchaseOrder->total,
            'due_date' => $purchaseOrder->received_at->copy()->addDays(30),
            'status' => PayableStatus::Pending,
        ]);
    }
}
