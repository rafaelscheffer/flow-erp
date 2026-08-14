<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\Models\ProductCategory;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductCategoryApiTest extends TestCase
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

    public function test_listing_categories_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/categories')->assertUnauthorized();
    }

    public function test_listing_categories_with_a_token_lacking_the_ability_is_forbidden(): void
    {
        $user = $this->userWithPermissions(['categories.view']);
        $token = $user->createToken('phpunit', ['categories.create'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/categories')->assertForbidden();
    }

    public function test_user_can_manage_categories_including_a_parent_relationship(): void
    {
        $user = $this->userWithPermissions(['categories.view', 'categories.create', 'categories.update', 'categories.delete']);
        $token = $user->createToken('phpunit', ['categories.view', 'categories.create', 'categories.update', 'categories.delete'])->plainTextToken;

        $parent = ProductCategory::factory()->create();

        $created = $this->withToken($token)->postJson('/api/v1/categories', [
            'name' => 'Subcategoria',
            'parent_id' => $parent->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.parent_id', $parent->id)
            ->json('data');

        $this->withToken($token)->putJson("/api/v1/categories/{$created['id']}", ['name' => 'Subcategoria Renomeada'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Subcategoria Renomeada');

        $this->withToken($token)->deleteJson("/api/v1/categories/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('product_categories', ['id' => $created['id']]);
    }
}
