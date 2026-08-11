<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductCategoryAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_categories_resource(): void
    {
        Permission::query()->create(['name' => 'categories.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('categories.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/product-categories')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_categories_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/product-categories')->assertForbidden();
    }
}
