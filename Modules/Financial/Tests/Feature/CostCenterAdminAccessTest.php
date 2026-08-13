<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\CostCenter;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CostCenterAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_cost_centers_resource(): void
    {
        Permission::query()->create(['name' => 'cost-centers.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('cost-centers.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/cost-centers')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_cost_centers_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/cost-centers')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_cost_center(): void
    {
        foreach (['cost-centers.view', 'cost-centers.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['cost-centers.view', 'cost-centers.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/cost-centers/create')->assertOk();
    }

    public function test_creating_a_cost_center_records_an_audit_entry(): void
    {
        $costCenter = CostCenter::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => CostCenter::class,
            'auditable_id' => $costCenter->id,
            'event' => 'created',
        ]);
    }
}
