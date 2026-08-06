<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Administration\Database\Seeders\AdministrationSeeder;
use Modules\Customers\Database\Seeders\CustomersSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdministrationSeeder::class);
        $this->call(CustomersSeeder::class);
    }
}
