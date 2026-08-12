<?php

declare(strict_types=1);

namespace Modules\Purchases\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Purchases\Models\Supplier;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_suppliers_resource(): void
    {
        Permission::query()->create(['name' => 'suppliers.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('suppliers.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/suppliers')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_suppliers_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/suppliers')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_supplier(): void
    {
        foreach (['suppliers.view', 'suppliers.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['suppliers.view', 'suppliers.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/suppliers/create')->assertOk();
    }

    public function test_creating_a_supplier_records_an_audit_entry(): void
    {
        $supplier = Supplier::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Supplier::class,
            'auditable_id' => $supplier->id,
            'event' => 'created',
        ]);
    }
}
