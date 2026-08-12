<?php

declare(strict_types=1);

namespace Modules\Purchases\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Purchases\Models\PurchaseOrder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PurchaseOrderAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_purchase_orders_resource(): void
    {
        Permission::query()->create(['name' => 'purchase-orders.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('purchase-orders.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/purchase-orders')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_purchase_orders_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/purchase-orders')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_purchase_order(): void
    {
        foreach (['purchase-orders.view', 'purchase-orders.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['purchase-orders.view', 'purchase-orders.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/purchase-orders/create')->assertOk();
    }

    public function test_creating_a_purchase_order_records_an_audit_entry(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => PurchaseOrder::class,
            'auditable_id' => $purchaseOrder->id,
            'event' => 'created',
        ]);
    }
}
