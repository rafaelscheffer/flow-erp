<?php

declare(strict_types=1);

namespace Modules\Customers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customers\Enums\CustomerType;
use Modules\Customers\Models\Customer;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $type = fake()->randomElement(CustomerType::cases());

        return [
            'type' => $type,
            'name' => $type === CustomerType::Company ? fake()->company() : fake()->name(),
            'trade_name' => $type === CustomerType::Company ? fake()->company() : null,
            'document' => $type === CustomerType::Company ? fake()->cnpj(false) : fake()->cpf(false),
            'state_registration' => $type === CustomerType::Company ? fake()->numerify('###.###.###.###') : null,
            'birth_date' => $type === CustomerType::Individual ? fake()->date() : null,
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
