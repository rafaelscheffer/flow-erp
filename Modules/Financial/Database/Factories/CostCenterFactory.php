<?php

declare(strict_types=1);

namespace Modules\Financial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Financial\Models\CostCenter;

class CostCenterFactory extends Factory
{
    protected $model = CostCenter::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('CC-###'),
            'name' => fake()->words(2, true),
            'is_active' => true,
        ];
    }
}
