<?php

declare(strict_types=1);

namespace Modules\Purchases\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PurchasesSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',
            'purchase-orders.view',
            'purchase-orders.create',
            'purchase-orders.update',
            'purchase-orders.delete',
            'purchase-orders.receive',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);
    }
}
