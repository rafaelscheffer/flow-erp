<?php

declare(strict_types=1);

namespace Modules\Purchases\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockMovement;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Events\PurchaseOrderReceived;
use Modules\Purchases\Models\PurchaseOrder;

/**
 * Receiving a purchase order is the one place stock is allowed to increase
 * from Purchases: it writes one "entrada" movement per item atomically and
 * flips the order to Recebido. Never call StockMovement::create() directly
 * from a Filament form here — this action is the sole entry point so the
 * order status and the stock ledger can never drift apart.
 */
class ReceivePurchaseOrderAction
{
    public function execute(PurchaseOrder $purchaseOrder, int $performedBy): PurchaseOrder
    {
        if ($purchaseOrder->status !== PurchaseOrderStatus::Sent) {
            throw ValidationException::withMessages([
                'status' => 'Apenas pedidos enviados podem ser recebidos.',
            ]);
        }

        return DB::transaction(function () use ($purchaseOrder, $performedBy): PurchaseOrder {
            foreach ($purchaseOrder->items as $item) {
                StockMovement::create([
                    'type' => StockMovementType::Entrada,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'stock_location_id' => $purchaseOrder->stock_location_id,
                    'quantity' => $item->quantity,
                    'notes' => "Recebimento do pedido de compra #{$purchaseOrder->id}",
                    'performed_by' => $performedBy,
                ]);
            }

            $purchaseOrder->update([
                'status' => PurchaseOrderStatus::Received,
                'received_at' => now(),
            ]);

            PurchaseOrderReceived::dispatch($purchaseOrder);

            return $purchaseOrder;
        });
    }
}
