<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\Models\Product;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductApiTest extends TestCase
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

    public function test_listing_products_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/products')->assertUnauthorized();
    }

    public function test_listing_products_with_a_token_lacking_the_ability_is_forbidden(): void
    {
        $user = $this->userWithPermissions(['products.view']);
        $token = $user->createToken('phpunit', ['products.create'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/products')->assertForbidden();
    }

    public function test_user_can_list_and_manage_products(): void
    {
        $user = $this->userWithPermissions(['products.view', 'products.create', 'products.update', 'products.delete']);
        $token = $user->createToken('phpunit', ['products.view', 'products.create', 'products.update', 'products.delete'])->plainTextToken;

        Product::factory()->count(2)->create();
        $this->withToken($token)->getJson('/api/v1/products')->assertOk()->assertJsonCount(2, 'data');

        $created = $this->withToken($token)->postJson('/api/v1/products', [
            'type' => 'simple',
            'name' => 'Camiseta Básica',
            'sale_price' => 49.90,
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Camiseta Básica')
            ->json('data');

        $this->withToken($token)->putJson("/api/v1/products/{$created['id']}", ['sale_price' => 59.90])
            ->assertOk()
            ->assertJsonPath('data.sale_price', '59.90');

        $this->withToken($token)->deleteJson("/api/v1/products/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('products', ['id' => $created['id']]);
    }

    public function test_creating_a_product_requires_a_valid_type(): void
    {
        $user = $this->userWithPermissions(['products.create']);
        $token = $user->createToken('phpunit', ['products.create'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/products', [
            'type' => 'invalid-type',
            'name' => 'Produto Teste',
        ])->assertUnprocessable();
    }
}
