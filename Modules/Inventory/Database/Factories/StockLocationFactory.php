<?php

declare(strict_types=1);

namespace Modules\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Models\StockLocation;

class StockLocationFactory extends Factory
{
    protected $model = StockLocation::class;

    public function definition(): array
    {
        return [
            'name' => 'Depósito '.fake()->unique()->city(),
            'code' => fake()->unique()->numerify('DEP-###'),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
