<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockLocation;
use Modules\Inventory\Models\StockMovement;
use Modules\Products\Models\Product;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockMovementApiTest extends TestCase
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

    public function test_listing_movements_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/movements')->assertUnauthorized();
    }

    public function test_user_can_list_and_register_stock_movements(): void
    {
        $user = $this->userWithPermissions(['movements.view', 'movements.create']);
        $token = $user->createToken('phpunit', ['movements.view', 'movements.create'])->plainTextToken;

        StockMovement::factory()->count(2)->create();
        $this->withToken($token)->getJson('/api/v1/movements')->assertOk()->assertJsonCount(2, 'data');

        $product = Product::factory()->create();
        $location = StockLocation::factory()->create();

        $this->withToken($token)->postJson('/api/v1/movements', [
            'type' => 'entrada',
            'stock_location_id' => $location->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ])->assertCreated()->assertJsonPath('data.quantity', 10);

        $this->assertDatabaseHas('stock_balances', [
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => 10,
        ]);
    }

    public function test_registering_an_entrada_movement_requires_a_positive_quantity(): void
    {
        $user = $this->userWithPermissions(['movements.create']);
        $token = $user->createToken('phpunit', ['movements.create'])->plainTextToken;
        $product = Product::factory()->create();
        $location = StockLocation::factory()->create();

        $this->withToken($token)->postJson('/api/v1/movements', [
            'type' => 'entrada',
            'stock_location_id' => $location->id,
            'product_id' => $product->id,
            'quantity' => -5,
        ])->assertUnprocessable();
    }

    public function test_movements_have_no_update_or_delete_routes(): void
    {
        $user = $this->userWithPermissions(['movements.view']);
        $token = $user->createToken('phpunit', ['movements.view'])->plainTextToken;
        $movement = StockMovement::factory()->create();

        $this->withToken($token)->putJson("/api/v1/movements/{$movement->id}", [])->assertMethodNotAllowed();
        $this->withToken($token)->deleteJson("/api/v1/movements/{$movement->id}")->assertMethodNotAllowed();
    }
}
