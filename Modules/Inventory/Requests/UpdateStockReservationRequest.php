<?php

declare(strict_types=1);

namespace Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Inventory\Enums\StockReservationStatus;

class UpdateStockReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reservations.update');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', 'string', 'in:'.implode(',', array_column(StockReservationStatus::cases(), 'value'))],
            'notes' => ['nullable', 'string'],
        ];
    }
}
