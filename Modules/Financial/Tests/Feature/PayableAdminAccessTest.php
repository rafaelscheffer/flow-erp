<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Payable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayableAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_payables_resource(): void
    {
        Permission::query()->create(['name' => 'payables.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('payables.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/payables')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_payables_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/payables')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_payable(): void
    {
        foreach (['payables.view', 'payables.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['payables.view', 'payables.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/payables/create')->assertOk();
    }

    public function test_creating_a_payable_records_an_audit_entry(): void
    {
        $payable = Payable::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Payable::class,
            'auditable_id' => $payable->id,
            'event' => 'created',
        ]);
    }
}
