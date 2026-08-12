<?php

declare(strict_types=1);

namespace Modules\Sales\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockMovement;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Events\OrderConfirmed;
use Modules\Sales\Models\Order;

/**
 * Confirming a sale is the one place stock is allowed to decrease from
 * Sales: it writes one "saida" movement per item atomically and flips the
 * order to Confirmado. Never call StockMovement::create() directly from a
 * Filament form here — this action is the sole entry point so the order
 * status and the stock ledger can never drift apart.
 */
class ConfirmSaleAction
{
    public function execute(Order $order, int $performedBy): Order
    {
        if ($order->status !== OrderStatus::Draft) {
            throw ValidationException::withMessages([
                'status' => 'Apenas pedidos em rascunho podem ser confirmados.',
            ]);
        }

        return DB::transaction(function () use ($order, $performedBy): Order {
            foreach ($order->items as $item) {
                StockMovement::create([
                    'type' => StockMovementType::Saida,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'stock_location_id' => $order->stock_location_id,
                    'quantity' => -$item->quantity,
                    'notes' => "Confirmação do pedido de venda #{$order->id}",
                    'performed_by' => $performedBy,
                ]);
            }

            $order->update([
                'status' => OrderStatus::Confirmed,
                'confirmed_at' => now(),
            ]);

            OrderConfirmed::dispatch($order);

            return $order;
        });
    }
}
