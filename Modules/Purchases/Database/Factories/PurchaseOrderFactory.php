<?php

declare(strict_types=1);

namespace Modules\Purchases\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Models\StockLocation;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\Supplier;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'stock_location_id' => StockLocation::factory(),
            'status' => PurchaseOrderStatus::Draft,
            'order_date' => fake()->date(),
            'expected_date' => null,
            'notes' => null,
            'created_by' => User::factory(),
            'received_at' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(['status' => PurchaseOrderStatus::Sent]);
    }

    public function received(): static
    {
        return $this->state([
            'status' => PurchaseOrderStatus::Received,
            'received_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => PurchaseOrderStatus::Cancelled]);
    }
}
