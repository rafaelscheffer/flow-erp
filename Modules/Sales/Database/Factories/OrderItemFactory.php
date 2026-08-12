<?php

declare(strict_types=1);

namespace Modules\Sales\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Products\Models\Product;
use Modules\Sales\Models\Order;
use Modules\Sales\Models\OrderItem;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'quantity' => fake()->numberBetween(1, 20),
            'unit_price' => fake()->randomFloat(2, 1, 500),
        ];
    }
}
