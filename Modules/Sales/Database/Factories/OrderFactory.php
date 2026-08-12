<?php

declare(strict_types=1);

namespace Modules\Sales\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\StockLocation;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Enums\PaymentMethod;
use Modules\Sales\Models\Order;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'stock_location_id' => StockLocation::factory(),
            'status' => OrderStatus::Draft,
            'order_date' => fake()->date(),
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'payment_method' => PaymentMethod::Pix,
            'notes' => null,
            'created_by' => User::factory(),
            'confirmed_at' => null,
            'invoiced_at' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state([
            'status' => OrderStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
    }

    public function invoiced(): static
    {
        return $this->state([
            'status' => OrderStatus::Invoiced,
            'confirmed_at' => now(),
            'invoiced_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => OrderStatus::Cancelled]);
    }
}
