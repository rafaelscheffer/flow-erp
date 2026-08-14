<?php

declare(strict_types=1);

namespace Modules\Reports\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_inventory_report(): void
    {
        Permission::query()->create(['name' => 'reports.inventory.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reports.inventory.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/inventory-report')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_inventory_report(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/inventory-report')->assertForbidden();
    }
}
