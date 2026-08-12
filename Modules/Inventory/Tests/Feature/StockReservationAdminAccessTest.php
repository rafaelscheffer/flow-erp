<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockReservation;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockReservationAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_stock_reservations_resource(): void
    {
        Permission::query()->create(['name' => 'reservations.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reservations.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/stock-reservations')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_stock_reservations_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/stock-reservations')->assertForbidden();
    }

    public function test_creating_a_stock_reservation_records_an_audit_entry(): void
    {
        $reservation = StockReservation::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => StockReservation::class,
            'auditable_id' => $reservation->id,
            'event' => 'created',
        ]);
    }
}
