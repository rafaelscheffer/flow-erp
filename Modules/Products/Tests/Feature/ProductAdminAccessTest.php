<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\Models\Product;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_products_resource(): void
    {
        Permission::query()->create(['name' => 'products.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('products.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/products')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_products_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/products')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_product(): void
    {
        foreach (['products.view', 'products.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['products.view', 'products.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/products/create')->assertOk();
    }

    public function test_creating_a_product_records_an_audit_entry(): void
    {
        $product = Product::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Product::class,
            'auditable_id' => $product->id,
            'event' => 'created',
        ]);
    }
}
