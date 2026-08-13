<?php

declare(strict_types=1);

namespace Modules\Financial\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FinancialSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'receivables.view',
            'receivables.create',
            'receivables.update',
            'receivables.delete',
            'receivables.mark-paid',
            'payables.view',
            'payables.create',
            'payables.update',
            'payables.delete',
            'payables.mark-paid',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);
    }
}
