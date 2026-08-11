<?php

declare(strict_types=1);

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            'brands.view',
            'brands.create',
            'brands.update',
            'brands.delete',
            'collections.view',
            'collections.create',
            'collections.update',
            'collections.delete',
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);
    }
}
