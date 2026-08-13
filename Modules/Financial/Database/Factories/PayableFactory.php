<?php

declare(strict_types=1);

namespace Modules\Financial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Models\Payable;
use Modules\Purchases\Models\Supplier;

class PayableFactory extends Factory
{
    protected $model = Payable::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'purchase_order_id' => null,
            'description' => null,
            'amount' => fake()->randomFloat(2, 50, 5000),
            'due_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'status' => PayableStatus::Pending,
            'paid_at' => null,
            'payment_method' => null,
            'created_by' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state([
            'status' => PayableStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => PayableStatus::Cancelled]);
    }
}
