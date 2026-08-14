<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockLocation;
use Modules\Inventory\Models\StockReservation;
use Modules\Products\Models\Product;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockReservationApiTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermissions(array $permissions): User
    {
        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $role = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo($permissions);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_listing_reservations_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/reservations')->assertUnauthorized();
    }

    public function test_user_can_create_and_update_a_reservation(): void
    {
        $user = $this->userWithPermissions(['reservations.view', 'reservations.create', 'reservations.update']);
        $token = $user->createToken('phpunit', ['reservations.view', 'reservations.create', 'reservations.update'])->plainTextToken;

        $product = Product::factory()->create();
        $location = StockLocation::factory()->create();

        $created = $this->withToken($token)->postJson('/api/v1/reservations', [
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => 5,
        ])->assertCreated()->assertJsonPath('data.status', 'active')->json('data');

        $this->withToken($token)->putJson("/api/v1/reservations/{$created['id']}", ['status' => 'released'])
            ->assertOk()
            ->assertJsonPath('data.status', 'released');
    }

    public function test_reservations_have_no_delete_route(): void
    {
        $user = $this->userWithPermissions(['reservations.view']);
        $token = $user->createToken('phpunit', ['reservations.view'])->plainTextToken;
        $reservation = StockReservation::factory()->create();

        $this->withToken($token)->deleteJson("/api/v1/reservations/{$reservation->id}")->assertMethodNotAllowed();
    }
}
