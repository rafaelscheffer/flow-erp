<?php

declare(strict_types=1);

namespace Modules\Customers\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Models\Customer;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerApiTest extends TestCase
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

    public function test_listing_customers_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/customers')->assertUnauthorized();
    }

    public function test_listing_customers_with_a_token_lacking_the_ability_is_forbidden(): void
    {
        $user = $this->userWithPermissions(['customers.view']);
        $token = $user->createToken('phpunit', ['customers.create'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/customers')->assertForbidden();
    }

    public function test_listing_customers_with_the_ability_but_no_real_permission_is_forbidden(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('phpunit', ['customers.view'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/customers')->assertForbidden();
    }

    public function test_user_with_permission_and_ability_can_list_customers(): void
    {
        $user = $this->userWithPermissions(['customers.view']);
        $token = $user->createToken('phpunit', ['customers.view'])->plainTextToken;
        Customer::factory()->count(2)->create();

        $this->withToken($token)->getJson('/api/v1/customers')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_create_a_customer(): void
    {
        $user = $this->userWithPermissions(['customers.create']);
        $token = $user->createToken('phpunit', ['customers.create'])->plainTextToken;

        $payload = [
            'type' => 'individual',
            'name' => 'João da Silva',
            'document' => '52998224725',
        ];

        $response = $this->withToken($token)->postJson('/api/v1/customers', $payload);

        $response->assertCreated()->assertJsonPath('data.name', 'João da Silva');
        $this->assertDatabaseHas('customers', ['document' => '52998224725']);
    }

    public function test_creating_a_customer_validates_the_document(): void
    {
        $user = $this->userWithPermissions(['customers.create']);
        $token = $user->createToken('phpunit', ['customers.create'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/customers', [
            'type' => 'individual',
            'name' => 'João da Silva',
            'document' => '00000000000',
        ])->assertUnprocessable();
    }

    public function test_user_can_update_a_customer(): void
    {
        $user = $this->userWithPermissions(['customers.update']);
        $token = $user->createToken('phpunit', ['customers.update'])->plainTextToken;
        $customer = Customer::factory()->create();

        $this->withToken($token)->putJson("/api/v1/customers/{$customer->id}", ['name' => 'Novo Nome'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Novo Nome');
    }

    public function test_user_can_delete_a_customer(): void
    {
        $user = $this->userWithPermissions(['customers.delete']);
        $token = $user->createToken('phpunit', ['customers.delete'])->plainTextToken;
        $customer = Customer::factory()->create();

        $this->withToken($token)->deleteJson("/api/v1/customers/{$customer->id}")->assertNoContent();
        $this->assertSoftDeleted($customer);
    }
}
