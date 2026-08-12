<?php

declare(strict_types=1);

namespace Modules\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\Sales\Models\Order;

/**
 * Financial's not built yet — this is the extension point a future
 * "GenerateReceivableForConfirmedOrder" listener hooks into instead of
 * ConfirmSaleAction reaching into a module that doesn't exist.
 */
class OrderConfirmed
{
    use Dispatchable;

    public function __construct(public readonly Order $order) {}
}
