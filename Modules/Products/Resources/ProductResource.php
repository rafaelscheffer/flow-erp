<?php

declare(strict_types=1);

namespace Modules\Products\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_category_id' => $this->product_category_id,
            'brand_id' => $this->brand_id,
            'product_collection_id' => $this->product_collection_id,
            'type' => $this->type,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'internal_code' => $this->internal_code,
            'sku' => $this->sku,
            'ean' => $this->ean,
            'ncm' => $this->ncm,
            'weight' => $this->weight,
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
            'promotional_price' => $this->promotional_price,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
