<?php

declare(strict_types=1);

namespace Modules\Sales\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'stock_location_id' => $this->stock_location_id,
            'status' => $this->status,
            'order_date' => $this->order_date?->toDateString(),
            'discount_amount' => $this->discount_amount,
            'shipping_amount' => $this->shipping_amount,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'confirmed_at' => $this->confirmed_at,
            'invoiced_at' => $this->invoiced_at,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
