<?php

declare(strict_types=1);

namespace Modules\Administration\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdministrationSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'audit-logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions($permissions);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@flowerp.test'],
            ['name' => 'Administrador', 'password' => 'password'],
        );

        $admin->assignRole($superAdmin);
    }
}
