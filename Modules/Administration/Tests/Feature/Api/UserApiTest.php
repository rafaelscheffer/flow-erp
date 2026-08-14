<?php

declare(strict_types=1);

namespace Modules\Administration\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserApiTest extends TestCase
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

    public function test_listing_users_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/users')->assertUnauthorized();
    }

    public function test_user_can_create_a_user_with_a_role(): void
    {
        $admin = $this->userWithPermissions(['users.view', 'users.create']);
        $token = $admin->createToken('phpunit', ['users.view', 'users.create'])->plainTextToken;
        $role = Role::query()->create(['name' => 'Vendedor']);

        $response = $this->withToken($token)->postJson('/api/v1/users', [
            'name' => 'Novo Usuário',
            'email' => 'novo@flowerp.test',
            'password' => 'password123',
            'roles' => ['Vendedor'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'novo@flowerp.test')
            ->assertJsonPath('data.roles.0', 'Vendedor');
        $this->assertDatabaseHas('users', ['email' => 'novo@flowerp.test']);
    }

    public function test_a_user_cannot_delete_their_own_account(): void
    {
        $admin = $this->userWithPermissions(['users.delete']);
        $token = $admin->createToken('phpunit', ['users.delete'])->plainTextToken;

        $this->withToken($token)->deleteJson("/api/v1/users/{$admin->id}")->assertForbidden();
    }

    public function test_a_user_can_delete_another_user(): void
    {
        $admin = $this->userWithPermissions(['users.delete']);
        $token = $admin->createToken('phpunit', ['users.delete'])->plainTextToken;
        $other = User::factory()->create();

        $this->withToken($token)->deleteJson("/api/v1/users/{$other->id}")->assertNoContent();
    }
}
