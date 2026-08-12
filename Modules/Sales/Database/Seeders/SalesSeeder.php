<?php

declare(strict_types=1);

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.delete',
            'orders.confirm',
            'orders.invoice',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);
    }
}
