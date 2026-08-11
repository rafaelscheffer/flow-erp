<?php

declare(strict_types=1);

namespace Modules\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory()->variable(),
            'sku' => fake()->unique()->numerify('SKU-#####-??'),
            'ean' => fake()->unique()->ean13(),
            'color' => fake()->safeColorName(),
            'size' => fake()->randomElement(['P', 'M', 'G', 'GG']),
            'cost_price' => null,
            'sale_price' => null,
            'promotional_price' => null,
            'weight' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
