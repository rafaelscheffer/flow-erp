<?php

declare(strict_types=1);

namespace Modules\Purchases\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\StockLocation;
use Modules\Products\Models\Product;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\Supplier;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PurchaseOrderApiTest extends TestCase
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

    public function test_listing_purchase_orders_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/purchase-orders')->assertUnauthorized();
    }

    public function test_user_can_create_a_purchase_order_with_items(): void
    {
        $user = $this->userWithPermissions(['purchase-orders.create']);
        $token = $user->createToken('phpunit', ['purchase-orders.create'])->plainTextToken;

        $supplier = Supplier::factory()->create();
        $location = StockLocation::factory()->create();
        $product = Product::factory()->create();

        $response = $this->withToken($token)->postJson('/api/v1/purchase-orders', [
            'supplier_id' => $supplier->id,
            'stock_location_id' => $location->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 10],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.total', 50);
    }

    public function test_a_received_purchase_order_cannot_be_updated(): void
    {
        $user = $this->userWithPermissions(['purchase-orders.update']);
        $token = $user->createToken('phpunit', ['purchase-orders.update'])->plainTextToken;
        $purchaseOrder = PurchaseOrder::factory()->received()->create();

        $this->withToken($token)->putJson("/api/v1/purchase-orders/{$purchaseOrder->id}", ['notes' => 'x'])
            ->assertUnprocessable();
    }

    public function test_receiving_a_sent_purchase_order_creates_a_stock_movement(): void
    {
        $user = $this->userWithPermissions(['purchase-orders.receive']);
        $token = $user->createToken('phpunit', ['purchase-orders.receive'])->plainTextToken;

        $location = StockLocation::factory()->create();
        $product = Product::factory()->create();
        $purchaseOrder = PurchaseOrder::factory()->sent()->create(['stock_location_id' => $location->id]);
        $purchaseOrder->items()->create(['product_id' => $product->id, 'quantity' => 4, 'unit_cost' => 8]);

        $response = $this->withToken($token)->postJson("/api/v1/purchase-orders/{$purchaseOrder->id}/receive");

        $response->assertOk()->assertJsonPath('data.status', 'received');

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => 4,
        ]);
    }

    public function test_receiving_a_draft_purchase_order_fails(): void
    {
        $user = $this->userWithPermissions(['purchase-orders.receive']);
        $token = $user->createToken('phpunit', ['purchase-orders.receive'])->plainTextToken;
        $purchaseOrder = PurchaseOrder::factory()->create();

        $this->withToken($token)->postJson("/api/v1/purchase-orders/{$purchaseOrder->id}/receive")->assertUnprocessable();
    }

    public function test_user_can_delete_a_draft_purchase_order(): void
    {
        $user = $this->userWithPermissions(['purchase-orders.delete']);
        $token = $user->createToken('phpunit', ['purchase-orders.delete'])->plainTextToken;
        $purchaseOrder = PurchaseOrder::factory()->create();

        $this->withToken($token)->deleteJson("/api/v1/purchase-orders/{$purchaseOrder->id}")->assertNoContent();
        $this->assertSoftDeleted($purchaseOrder);
    }
}
