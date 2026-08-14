<?php

declare(strict_types=1);

namespace Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Inventory\Enums\StockMovementType;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('movements.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isDirectional = in_array($this->input('type'), [
            StockMovementType::Entrada->value,
            StockMovementType::Saida->value,
        ], true);

        return [
            'type' => ['required', 'string', 'in:'.implode(',', array_column(StockMovementType::creatableCases(), 'value'))],
            'stock_location_id' => ['required', 'integer', 'exists:stock_locations,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', $isDirectional ? 'min:1' : 'not_in:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): mixed
    {
        return array_merge(parent::validated($key, $default), [
            'performed_by' => $this->user()->id,
        ]);
    }
}
