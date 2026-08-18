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

    public function test_a_user_cannot_assign_a_role_that_grants_permissions_they_do_not_have(): void
    {
        Permission::query()->firstOrCreate(['name' => 'users.update']);
        Permission::query()->firstOrCreate(['name' => 'payables.delete']);

        $lowPrivilege = Role::query()->create(['name' => 'Suporte Basico']);
        $lowPrivilege->givePermissionTo('users.update');

        $actor = User::factory()->create();
        $actor->assignRole($lowPrivilege);
        $token = $actor->createToken('phpunit', ['users.update'])->plainTextToken;

        $superAdmin = Role::query()->create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo('payables.delete');

        $response = $this->withToken($token)->putJson("/api/v1/users/{$actor->id}", [
            'roles' => ['Super Admin'],
        ]);

        $response->assertUnprocessable();
        $this->assertFalse($actor->fresh()->hasRole('Super Admin'));
    }

    public function test_a_user_can_assign_a_role_whose_permissions_they_already_have(): void
    {
        Permission::query()->firstOrCreate(['name' => 'users.update']);
        Permission::query()->firstOrCreate(['name' => 'orders.view']);

        $actorRole = Role::query()->create(['name' => 'Gestor de Usuarios']);
        $actorRole->givePermissionTo(['users.update', 'orders.view']);

        $actor = User::factory()->create();
        $actor->assignRole($actorRole);
        $token = $actor->createToken('phpunit', ['users.update'])->plainTextToken;

        $vendedor = Role::query()->create(['name' => 'Vendedor']);
        $vendedor->givePermissionTo('orders.view');
        $other = User::factory()->create();

        $response = $this->withToken($token)->putJson("/api/v1/users/{$other->id}", [
            'roles' => ['Vendedor'],
        ]);

        $response->assertOk();
        $this->assertTrue($other->fresh()->hasRole('Vendedor'));
    }
}
