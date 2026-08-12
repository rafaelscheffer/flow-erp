<?php

declare(strict_types=1);

namespace Modules\Inventory\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockLocation;
use Modules\Inventory\Models\StockMovement;
use Modules\Products\Models\Product;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'type' => StockMovementType::Entrada,
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'stock_location_id' => StockLocation::factory(),
            'quantity' => fake()->numberBetween(1, 50),
            'transfer_group_id' => null,
            'notes' => fake()->optional()->sentence(),
            'performed_by' => User::factory(),
        ];
    }

    public function exit(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => StockMovementType::Saida,
            'quantity' => -abs($attributes['quantity'] ?? fake()->numberBetween(1, 50)),
        ]);
    }
}
