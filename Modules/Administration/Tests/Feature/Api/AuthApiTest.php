<?php

declare(strict_types=1);

namespace Modules\Administration\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_issuing_a_token_with_valid_credentials_returns_a_token_with_all_the_users_permissions(): void
    {
        Permission::query()->create(['name' => 'customers.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('customers.view');

        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $user->assignRole($role);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'abilities']);
        $this->assertSame(['customers.view'], $response->json('abilities'));
    }

    public function test_issuing_a_token_with_invalid_credentials_fails(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'phpunit',
        ])->assertUnprocessable();
    }

    public function test_a_token_can_be_scoped_to_a_subset_of_the_users_permissions(): void
    {
        foreach (['customers.view', 'customers.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['customers.view', 'customers.create']);

        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $user->assignRole($role);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
            'abilities' => ['customers.view'],
        ]);

        $this->assertSame(['customers.view'], $response->json('abilities'));
    }

    public function test_me_returns_the_authenticated_user_with_roles_and_permissions(): void
    {
        Permission::query()->create(['name' => 'customers.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('customers.view');

        $user = User::factory()->create();
        $user->assignRole($role);
        $token = $user->createToken('phpunit', ['customers.view'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.roles.0', 'Super Admin')
            ->assertJsonPath('data.permissions.0', 'customers.view');
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('phpunit')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_issuing_a_token_is_rate_limited_after_repeated_attempts(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $payload = [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'phpunit',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/token', $payload)->assertUnprocessable();
        }

        $this->postJson('/api/v1/auth/token', $payload)->assertStatus(429);
    }
}
