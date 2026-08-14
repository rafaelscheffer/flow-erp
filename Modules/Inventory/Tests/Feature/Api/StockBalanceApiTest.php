<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockMovement;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockBalanceApiTest extends TestCase
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

    public function test_listing_balances_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/balances')->assertUnauthorized();
    }

    public function test_user_can_list_stock_balances(): void
    {
        $user = $this->userWithPermissions(['balances.view']);
        $token = $user->createToken('phpunit', ['balances.view'])->plainTextToken;

        StockMovement::factory()->create(['quantity' => 15]);

        $this->withToken($token)->getJson('/api/v1/balances')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.quantity', 15)
            ->assertJsonPath('data.0.available_quantity', 15);
    }

    public function test_balances_have_no_write_routes(): void
    {
        $user = $this->userWithPermissions(['balances.view']);
        $token = $user->createToken('phpunit', ['balances.view'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/balances', [])->assertMethodNotAllowed();
    }
}
