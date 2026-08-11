<?php

declare(strict_types=1);

namespace Modules\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $costPrice = fake()->randomFloat(2, 10, 200);

        return [
            'product_category_id' => null,
            'brand_id' => null,
            'product_collection_id' => null,
            'type' => ProductType::Simple,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'internal_code' => fake()->unique()->numerify('INT-#####'),
            'sku' => fake()->unique()->numerify('SKU-#####'),
            'ean' => fake()->unique()->ean13(),
            'ncm' => fake()->numerify('########'),
            'weight' => fake()->randomFloat(3, 0.1, 20),
            'height' => fake()->randomFloat(3, 1, 100),
            'width' => fake()->randomFloat(3, 1, 100),
            'length' => fake()->randomFloat(3, 1, 100),
            'cost_price' => $costPrice,
            'sale_price' => $costPrice * 1.5,
            'promotional_price' => null,
            'min_stock' => fake()->numberBetween(0, 10),
            'max_stock' => fake()->numberBetween(50, 200),
            'is_active' => true,
        ];
    }

    public function variable(): static
    {
        return $this->state([
            'type' => ProductType::Variable,
            'sku' => null,
            'ean' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
