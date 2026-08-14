<?php

declare(strict_types=1);

namespace Modules\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Products\Enums\ProductType;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('products.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'product_collection_id' => ['nullable', 'integer', 'exists:product_collections,id'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(ProductType::cases(), 'value'))],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'internal_code' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'ean' => ['nullable', 'string', 'max:255'],
            'ncm' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
            'width' => ['nullable', 'numeric'],
            'length' => ['nullable', 'numeric'],
            'cost_price' => ['nullable', 'numeric'],
            'sale_price' => ['nullable', 'numeric'],
            'promotional_price' => ['nullable', 'numeric'],
            'min_stock' => ['nullable', 'integer'],
            'max_stock' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
