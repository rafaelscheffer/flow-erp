<?php

declare(strict_types=1);

namespace Modules\Financial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customers\Models\Customer;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Receivable;

class ReceivableFactory extends Factory
{
    protected $model = Receivable::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_id' => null,
            'description' => null,
            'amount' => fake()->randomFloat(2, 50, 5000),
            'due_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'status' => ReceivableStatus::Pending,
            'paid_at' => null,
            'payment_method' => null,
            'created_by' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state([
            'status' => ReceivableStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => ReceivableStatus::Cancelled]);
    }
}
