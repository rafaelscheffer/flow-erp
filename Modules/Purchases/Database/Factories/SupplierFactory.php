<?php

declare(strict_types=1);

namespace Modules\Purchases\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Purchases\Enums\SupplierType;
use Modules\Purchases\Models\Supplier;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        $type = fake()->randomElement(SupplierType::cases());

        return [
            'type' => $type,
            'name' => $type === SupplierType::Company ? fake()->company() : fake()->name(),
            'trade_name' => $type === SupplierType::Company ? fake()->company() : null,
            'document' => $type === SupplierType::Company ? fake()->cnpj(false) : fake()->cpf(false),
            'state_registration' => $type === SupplierType::Company ? fake()->numerify('###.###.###.###') : null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('###########'),
            'zip_code' => fake()->numerify('########'),
            'address' => fake()->streetName(),
            'address_number' => fake()->buildingNumber(),
            'address_complement' => null,
            'neighborhood' => fake()->citySuffix(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'notes' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
