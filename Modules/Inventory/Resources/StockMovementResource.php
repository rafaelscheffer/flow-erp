<?php

declare(strict_types=1);

namespace Modules\Inventory\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'stock_location_id' => $this->stock_location_id,
            'quantity' => $this->quantity,
            'transfer_group_id' => $this->transfer_group_id,
            'notes' => $this->notes,
            'performed_by' => $this->performed_by,
            'created_at' => $this->created_at,
        ];
    }
}
