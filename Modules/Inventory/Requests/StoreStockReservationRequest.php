<?php

declare(strict_types=1);

namespace Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Inventory\Enums\StockReservationStatus;

class StoreStockReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reservations.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'stock_location_id' => ['required', 'integer', 'exists:stock_locations,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): mixed
    {
        return array_merge(parent::validated($key, $default), [
            'reserved_by' => $this->user()->id,
            'status' => StockReservationStatus::Active->value,
        ]);
    }
}
