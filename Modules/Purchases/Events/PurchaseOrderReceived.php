<?php

declare(strict_types=1);

namespace Modules\Purchases\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\Purchases\Models\PurchaseOrder;

/**
 * Extension point for Financial's "GeneratePayableForReceivedPurchaseOrder"
 * listener, so ReceivePurchaseOrderAction never has to reach into the
 * Financial module directly — mirrors Sales' OrderConfirmed event.
 */
class PurchaseOrderReceived
{
    use Dispatchable;

    public function __construct(public readonly PurchaseOrder $purchaseOrder) {}
}
