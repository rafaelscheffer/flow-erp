<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Administration\Database\Seeders\AdministrationSeeder;
use Modules\Customers\Database\Seeders\CustomersSeeder;
use Modules\Financial\Database\Seeders\FinancialSeeder;
use Modules\Inventory\Database\Seeders\InventorySeeder;
use Modules\Products\Database\Seeders\ProductsSeeder;
use Modules\Purchases\Database\Seeders\PurchasesSeeder;
use Modules\Reports\Database\Seeders\ReportsSeeder;
use Modules\Sales\Database\Seeders\SalesSeeder;

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
        $this->call(ProductsSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(PurchasesSeeder::class);
        $this->call(SalesSeeder::class);
        $this->call(FinancialSeeder::class);
        $this->call(ReportsSeeder::class);
    }
}
