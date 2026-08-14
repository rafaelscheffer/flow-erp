<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\Models\Brand;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandApiTest extends TestCase
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

    public function test_listing_brands_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/brands')->assertUnauthorized();
    }

    public function test_listing_brands_with_a_token_lacking_the_ability_is_forbidden(): void
    {
        $user = $this->userWithPermissions(['brands.view']);
        $token = $user->createToken('phpunit', ['brands.create'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/brands')->assertForbidden();
    }

    public function test_user_can_list_and_manage_brands(): void
    {
        $user = $this->userWithPermissions(['brands.view', 'brands.create', 'brands.update', 'brands.delete']);
        $token = $user->createToken('phpunit', ['brands.view', 'brands.create', 'brands.update', 'brands.delete'])->plainTextToken;

        Brand::factory()->count(2)->create();
        $this->withToken($token)->getJson('/api/v1/brands')->assertOk()->assertJsonCount(2, 'data');

        $created = $this->withToken($token)->postJson('/api/v1/brands', ['name' => 'Acme'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Acme')
            ->assertJsonPath('data.slug', 'acme')
            ->json('data');

        $this->withToken($token)->putJson("/api/v1/brands/{$created['id']}", ['name' => 'Acme Corp'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Acme Corp');

        $this->withToken($token)->deleteJson("/api/v1/brands/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('brands', ['id' => $created['id']]);
    }
}
