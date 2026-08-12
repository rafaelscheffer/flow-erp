<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockLocation;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockLocationAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_stock_locations_resource(): void
    {
        Permission::query()->create(['name' => 'locations.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('locations.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/stock-locations')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_stock_locations_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/stock-locations')->assertForbidden();
    }

    public function test_creating_a_stock_location_records_an_audit_entry(): void
    {
        $location = StockLocation::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => StockLocation::class,
            'auditable_id' => $location->id,
            'event' => 'created',
        ]);
    }
}
