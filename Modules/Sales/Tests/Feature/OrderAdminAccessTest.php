<?php

declare(strict_types=1);

namespace Modules\Sales\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sales\Models\Order;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_orders_resource(): void
    {
        Permission::query()->create(['name' => 'orders.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('orders.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/orders')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_orders_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/orders')->assertForbidden();
    }

    public function test_user_with_permission_can_create_an_order(): void
    {
        foreach (['orders.view', 'orders.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['orders.view', 'orders.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/orders/create')->assertOk();
    }

    public function test_creating_an_order_records_an_audit_entry(): void
    {
        $order = Order::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Order::class,
            'auditable_id' => $order->id,
            'event' => 'created',
        ]);
    }
}
