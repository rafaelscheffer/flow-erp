<?php

declare(strict_types=1);

namespace Modules\Products\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_brands_resource(): void
    {
        Permission::query()->create(['name' => 'brands.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('brands.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/brands')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_brands_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/brands')->assertForbidden();
    }
}
