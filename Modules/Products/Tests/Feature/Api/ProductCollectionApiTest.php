<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\Models\ProductCollection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductCollectionApiTest extends TestCase
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

    public function test_listing_collections_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/collections')->assertUnauthorized();
    }

    public function test_listing_collections_with_a_token_lacking_the_ability_is_forbidden(): void
    {
        $user = $this->userWithPermissions(['collections.view']);
        $token = $user->createToken('phpunit', ['collections.create'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/collections')->assertForbidden();
    }

    public function test_user_can_list_and_manage_collections(): void
    {
        $user = $this->userWithPermissions(['collections.view', 'collections.create', 'collections.update', 'collections.delete']);
        $token = $user->createToken('phpunit', ['collections.view', 'collections.create', 'collections.update', 'collections.delete'])->plainTextToken;

        ProductCollection::factory()->count(2)->create();
        $this->withToken($token)->getJson('/api/v1/collections')->assertOk()->assertJsonCount(2, 'data');

        $created = $this->withToken($token)->postJson('/api/v1/collections', ['name' => 'Verão 2026'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Verão 2026')
            ->json('data');

        $this->withToken($token)->putJson("/api/v1/collections/{$created['id']}", ['name' => 'Verão 2027'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Verão 2027');

        $this->withToken($token)->deleteJson("/api/v1/collections/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('product_collections', ['id' => $created['id']]);
    }
}
