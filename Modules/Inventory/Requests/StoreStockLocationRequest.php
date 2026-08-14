<?php

declare(strict_types=1);

namespace Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('locations.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:stock_locations,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
