<?php

declare(strict_types=1);

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'locations.view',
            'locations.create',
            'locations.update',
            'locations.delete',
            'movements.view',
            'movements.create',
            'balances.view',
            'reservations.view',
            'reservations.create',
            'reservations.update',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);
    }
}
