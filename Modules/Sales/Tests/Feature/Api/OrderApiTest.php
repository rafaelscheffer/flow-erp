<?php

declare(strict_types=1);

namespace Modules\Sales\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\StockLocation;
use Modules\Products\Models\Product;
use Modules\Sales\Models\Order;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderApiTest extends TestCase
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

    public function test_listing_orders_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/orders')->assertUnauthorized();
    }

    public function test_user_can_create_an_order_with_items(): void
    {
        $user = $this->userWithPermissions(['orders.view', 'orders.create']);
        $token = $user->createToken('phpunit', ['orders.view', 'orders.create'])->plainTextToken;

        $customer = Customer::factory()->create();
        $location = StockLocation::factory()->create();
        $product = Product::factory()->create(['sale_price' => 20]);

        $response = $this->withToken($token)->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'stock_location_id' => $location->id,
            'payment_method' => 'pix',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 20],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.subtotal', 60);
    }

    public function test_creating_an_order_requires_at_least_one_item(): void
    {
        $user = $this->userWithPermissions(['orders.create']);
        $token = $user->createToken('phpunit', ['orders.create'])->plainTextToken;
        $customer = Customer::factory()->create();
        $location = StockLocation::factory()->create();

        $this->withToken($token)->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'stock_location_id' => $location->id,
            'payment_method' => 'pix',
            'items' => [],
        ])->assertUnprocessable();
    }

    public function test_user_can_update_a_draft_order(): void
    {
        $user = $this->userWithPermissions(['orders.update']);
        $token = $user->createToken('phpunit', ['orders.update'])->plainTextToken;
        $order = Order::factory()->create();

        $this->withToken($token)->putJson("/api/v1/orders/{$order->id}", ['notes' => 'Entregar até sexta'])
            ->assertOk()
            ->assertJsonPath('data.notes', 'Entregar até sexta');
    }

    public function test_a_confirmed_order_cannot_be_updated(): void
    {
        $user = $this->userWithPermissions(['orders.update']);
        $token = $user->createToken('phpunit', ['orders.update'])->plainTextToken;
        $order = Order::factory()->confirmed()->create();

        $this->withToken($token)->putJson("/api/v1/orders/{$order->id}", ['notes' => 'x'])
            ->assertUnprocessable();
    }

    public function test_confirming_a_draft_order_creates_a_stock_movement_and_updates_status(): void
    {
        $user = $this->userWithPermissions(['orders.view', 'orders.create', 'orders.confirm']);
        $token = $user->createToken('phpunit', ['orders.view', 'orders.create', 'orders.confirm'])->plainTextToken;

        $customer = Customer::factory()->create();
        $location = StockLocation::factory()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'stock_location_id' => $location->id,
        ]);
        $order->items()->create(['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 10]);

        $response = $this->withToken($token)->postJson("/api/v1/orders/{$order->id}/confirm");

        $response->assertOk()->assertJsonPath('data.status', 'confirmed');

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => -2,
        ]);
        $this->assertDatabaseHas('stock_balances', [
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => -2,
        ]);
    }

    public function test_confirming_an_already_confirmed_order_fails(): void
    {
        $user = $this->userWithPermissions(['orders.confirm']);
        $token = $user->createToken('phpunit', ['orders.confirm'])->plainTextToken;
        $order = Order::factory()->confirmed()->create();

        $this->withToken($token)->postJson("/api/v1/orders/{$order->id}/confirm")->assertUnprocessable();
    }

    public function test_user_can_delete_a_draft_order(): void
    {
        $user = $this->userWithPermissions(['orders.delete']);
        $token = $user->createToken('phpunit', ['orders.delete'])->plainTextToken;
        $order = Order::factory()->create();

        $this->withToken($token)->deleteJson("/api/v1/orders/{$order->id}")->assertNoContent();
        $this->assertSoftDeleted($order);
    }
}
