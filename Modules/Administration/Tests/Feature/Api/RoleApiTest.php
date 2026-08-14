<?php

declare(strict_types=1);

namespace Modules\Administration\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleApiTest extends TestCase
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

    public function test_listing_roles_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/roles')->assertUnauthorized();
    }

    public function test_user_can_create_a_role_with_permissions(): void
    {
        $user = $this->userWithPermissions(['roles.view', 'roles.create']);
        $token = $user->createToken('phpunit', ['roles.view', 'roles.create'])->plainTextToken;
        Permission::query()->create(['name' => 'customers.view']);

        $response = $this->withToken($token)->postJson('/api/v1/roles', [
            'name' => 'Vendedor',
            'permissions' => ['customers.view'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Vendedor')
            ->assertJsonPath('data.permissions.0', 'customers.view');
    }

    public function test_user_can_update_a_roles_permissions(): void
    {
        $user = $this->userWithPermissions(['roles.update']);
        $token = $user->createToken('phpunit', ['roles.update'])->plainTextToken;
        Permission::query()->create(['name' => 'products.view']);
        $role = Role::query()->create(['name' => 'Estoquista']);

        $this->withToken($token)->putJson("/api/v1/roles/{$role->id}", ['permissions' => ['products.view']])
            ->assertOk()
            ->assertJsonPath('data.permissions.0', 'products.view');
    }
}
