<?php

declare(strict_types=1);

namespace Modules\Financial\Listeners;

use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Receivable;
use Modules\Sales\Events\OrderConfirmed;

class GenerateReceivableForConfirmedOrder
{
    public function handle(OrderConfirmed $event): void
    {
        $order = $event->order;

        Receivable::create([
            'customer_id' => $order->customer_id,
            'order_id' => $order->id,
            'amount' => $order->total,
            'due_date' => $order->confirmed_at->copy()->addDays(30),
            'status' => ReceivableStatus::Pending,
        ]);
    }
}
