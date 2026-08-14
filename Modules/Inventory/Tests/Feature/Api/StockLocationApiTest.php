<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockLocation;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockLocationApiTest extends TestCase
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

    public function test_listing_locations_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/locations')->assertUnauthorized();
    }

    public function test_listing_locations_with_a_token_lacking_the_ability_is_forbidden(): void
    {
        $user = $this->userWithPermissions(['locations.view']);
        $token = $user->createToken('phpunit', ['locations.create'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/locations')->assertForbidden();
    }

    public function test_user_can_manage_stock_locations(): void
    {
        $user = $this->userWithPermissions(['locations.view', 'locations.create', 'locations.update', 'locations.delete']);
        $token = $user->createToken('phpunit', ['locations.view', 'locations.create', 'locations.update', 'locations.delete'])->plainTextToken;

        StockLocation::factory()->count(2)->create();
        $this->withToken($token)->getJson('/api/v1/locations')->assertOk()->assertJsonCount(2, 'data');

        $created = $this->withToken($token)->postJson('/api/v1/locations', ['name' => 'Depósito Central', 'code' => 'DEP-001'])
            ->assertCreated()
            ->assertJsonPath('data.code', 'DEP-001')
            ->json('data');

        $this->withToken($token)->putJson("/api/v1/locations/{$created['id']}", ['name' => 'Depósito Renomeado'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Depósito Renomeado');

        $this->withToken($token)->deleteJson("/api/v1/locations/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('stock_locations', ['id' => $created['id']]);
    }

    public function test_creating_a_location_requires_a_unique_code(): void
    {
        $user = $this->userWithPermissions(['locations.create']);
        $token = $user->createToken('phpunit', ['locations.create'])->plainTextToken;
        StockLocation::factory()->create(['code' => 'DEP-001']);

        $this->withToken($token)->postJson('/api/v1/locations', ['name' => 'Outro', 'code' => 'DEP-001'])
            ->assertUnprocessable();
    }
}
