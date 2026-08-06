<?php

declare(strict_types=1);

namespace Modules\Customers\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);
    }
}
