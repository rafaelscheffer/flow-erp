<?php

declare(strict_types=1);

namespace Modules\Financial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Financial\Enums\AccountType;
use Modules\Financial\Models\Account;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'code' => fake()->unique()->numerify('#.#.##'),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(AccountType::cases()),
            'is_active' => true,
        ];
    }
}
