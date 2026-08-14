<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Account;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountApiTest extends TestCase
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

    public function test_listing_accounts_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/accounts')->assertUnauthorized();
    }

    public function test_user_can_manage_accounts(): void
    {
        $user = $this->userWithPermissions(['accounts.view', 'accounts.create', 'accounts.update', 'accounts.delete']);
        $token = $user->createToken('phpunit', ['accounts.view', 'accounts.create', 'accounts.update', 'accounts.delete'])->plainTextToken;

        $created = $this->withToken($token)->postJson('/api/v1/accounts', [
            'code' => '1',
            'name' => 'Ativo',
            'type' => 'asset',
        ])->assertCreated()->assertJsonPath('data.name', 'Ativo')->json('data');

        $this->withToken($token)->putJson("/api/v1/accounts/{$created['id']}", ['name' => 'Ativo Circulante'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Ativo Circulante');

        $this->withToken($token)->deleteJson("/api/v1/accounts/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('accounts', ['id' => $created['id']]);
    }

    public function test_an_account_cannot_become_its_own_descendants_parent(): void
    {
        $user = $this->userWithPermissions(['accounts.update']);
        $token = $user->createToken('phpunit', ['accounts.update'])->plainTextToken;

        $parent = Account::factory()->create();
        $child = Account::factory()->create(['parent_id' => $parent->id]);

        $this->withToken($token)->putJson("/api/v1/accounts/{$parent->id}", ['parent_id' => $child->id])
            ->assertUnprocessable();
    }
}
