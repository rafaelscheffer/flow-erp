<?php

declare(strict_types=1);

namespace Modules\Purchases\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'document' => $this->document,
            'state_registration' => $this->state_registration,
            'email' => $this->email,
            'phone' => $this->phone,
            'zip_code' => $this->zip_code,
            'address' => $this->address,
            'address_number' => $this->address_number,
            'address_complement' => $this->address_complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
