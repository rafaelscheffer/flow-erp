<?php

declare(strict_types=1);

namespace Modules\Inventory\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Enums\StockReservationStatus;
use Modules\Inventory\Models\StockLocation;
use Modules\Inventory\Models\StockReservation;
use Modules\Products\Models\Product;

class StockReservationFactory extends Factory
{
    protected $model = StockReservation::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'stock_location_id' => StockLocation::factory(),
            'quantity' => fake()->numberBetween(1, 20),
            'status' => StockReservationStatus::Active,
            'reserved_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function released(): static
    {
        return $this->state(['status' => StockReservationStatus::Released]);
    }
}
