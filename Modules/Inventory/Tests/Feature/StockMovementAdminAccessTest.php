<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockMovement;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockMovementAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_stock_movements_resource(): void
    {
        Permission::query()->create(['name' => 'movements.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('movements.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/stock-movements')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_stock_movements_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/stock-movements')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_stock_movement(): void
    {
        foreach (['movements.view', 'movements.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['movements.view', 'movements.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/stock-movements/create')->assertOk();
    }

    public function test_creating_a_stock_movement_records_an_audit_entry(): void
    {
        $movement = StockMovement::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => StockMovement::class,
            'auditable_id' => $movement->id,
            'event' => 'created',
        ]);
    }
}
