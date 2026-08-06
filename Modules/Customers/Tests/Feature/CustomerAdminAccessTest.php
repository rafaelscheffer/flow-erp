<?php

declare(strict_types=1);

namespace Modules\Customers\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Models\Customer;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_customers_resource(): void
    {
        Permission::query()->create(['name' => 'customers.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('customers.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/customers')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_customers_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/customers')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_customer(): void
    {
        foreach (['customers.view', 'customers.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['customers.view', 'customers.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/customers/create')->assertOk();
    }

    public function test_creating_a_customer_records_an_audit_entry(): void
    {
        $customer = Customer::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Customer::class,
            'auditable_id' => $customer->id,
            'event' => 'created',
        ]);
    }
}
