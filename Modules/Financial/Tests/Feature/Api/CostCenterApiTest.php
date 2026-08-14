<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CostCenterApiTest extends TestCase
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

    public function test_listing_cost_centers_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/cost-centers')->assertUnauthorized();
    }

    public function test_user_can_manage_cost_centers(): void
    {
        $user = $this->userWithPermissions(['cost-centers.view', 'cost-centers.create', 'cost-centers.update', 'cost-centers.delete']);
        $token = $user->createToken('phpunit', ['cost-centers.view', 'cost-centers.create', 'cost-centers.update', 'cost-centers.delete'])->plainTextToken;

        $created = $this->withToken($token)->postJson('/api/v1/cost-centers', ['code' => 'CC-1', 'name' => 'Comercial'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Comercial')
            ->json('data');

        $this->withToken($token)->putJson("/api/v1/cost-centers/{$created['id']}", ['name' => 'Comercial Renomeado'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Comercial Renomeado');

        $this->withToken($token)->deleteJson("/api/v1/cost-centers/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('cost_centers', ['id' => $created['id']]);
    }
}
