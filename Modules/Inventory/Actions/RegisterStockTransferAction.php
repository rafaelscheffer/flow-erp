<?php

declare(strict_types=1);

namespace Modules\Inventory\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockMovement;

/**
 * A transfer is never a single row: it always writes two linked "transferencia"
 * movements atomically (a negative leg at the origin, a positive leg at the
 * destination) sharing a transfer_group_id — this is the one case where stock
 * movements are written outside the generic Filament create form.
 */
class RegisterStockTransferAction
{
    public function execute(
        int $productId,
        ?int $productVariantId,
        int $fromLocationId,
        int $toLocationId,
        int $quantity,
        int $performedBy,
        ?string $notes = null,
    ): array {
        if ($fromLocationId === $toLocationId) {
            throw ValidationException::withMessages([
                'to_location_id' => 'O local de destino deve ser diferente do local de origem.',
            ]);
        }

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'A quantidade transferida deve ser maior que zero.',
            ]);
        }

        $transferGroupId = (string) Str::uuid();

        return DB::transaction(function () use (
            $productId,
            $productVariantId,
            $fromLocationId,
            $toLocationId,
            $quantity,
            $performedBy,
            $notes,
            $transferGroupId,
        ): array {
            $origin = StockMovement::create([
                'type' => StockMovementType::Transferencia,
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'stock_location_id' => $fromLocationId,
                'quantity' => -$quantity,
                'transfer_group_id' => $transferGroupId,
                'notes' => $notes,
                'performed_by' => $performedBy,
            ]);

            $destination = StockMovement::create([
                'type' => StockMovementType::Transferencia,
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'stock_location_id' => $toLocationId,
                'quantity' => $quantity,
                'transfer_group_id' => $transferGroupId,
                'notes' => $notes,
                'performed_by' => $performedBy,
            ]);

            return [$origin, $destination];
        });
    }
}
