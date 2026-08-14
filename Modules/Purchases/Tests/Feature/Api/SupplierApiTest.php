<?php

declare(strict_types=1);

namespace Modules\Purchases\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Purchases\Models\Supplier;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierApiTest extends TestCase
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

    public function test_listing_suppliers_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/suppliers')->assertUnauthorized();
    }

    public function test_listing_suppliers_with_a_token_lacking_the_ability_is_forbidden(): void
    {
        $user = $this->userWithPermissions(['suppliers.view']);
        $token = $user->createToken('phpunit', ['suppliers.create'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/suppliers')->assertForbidden();
    }

    public function test_user_can_create_and_manage_a_supplier(): void
    {
        $user = $this->userWithPermissions(['suppliers.view', 'suppliers.create', 'suppliers.update', 'suppliers.delete']);
        $token = $user->createToken('phpunit', ['suppliers.view', 'suppliers.create', 'suppliers.update', 'suppliers.delete'])->plainTextToken;

        Supplier::factory()->count(2)->create();
        $this->withToken($token)->getJson('/api/v1/suppliers')->assertOk()->assertJsonCount(2, 'data');

        $created = $this->withToken($token)->postJson('/api/v1/suppliers', [
            'type' => 'individual',
            'name' => 'João Fornecedor',
            'document' => '52998224725',
        ])->assertCreated()->assertJsonPath('data.name', 'João Fornecedor')->json('data');

        $this->withToken($token)->putJson("/api/v1/suppliers/{$created['id']}", ['name' => 'João Renomeado'])
            ->assertOk()
            ->assertJsonPath('data.name', 'João Renomeado');

        $this->withToken($token)->deleteJson("/api/v1/suppliers/{$created['id']}")->assertNoContent();
        $this->assertSoftDeleted('suppliers', ['id' => $created['id']]);
    }
}
