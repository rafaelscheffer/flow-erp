<?php

declare(strict_types=1);

namespace Modules\Reports\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ReportsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'reports.sales.view',
            'reports.purchases.view',
            'reports.customers.view',
            'reports.products.view',
            'reports.inventory.view',
            'reports.receivables.view',
            'reports.payables.view',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);
    }
}
