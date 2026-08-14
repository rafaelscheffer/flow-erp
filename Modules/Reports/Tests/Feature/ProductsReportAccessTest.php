<?php

declare(strict_types=1);

namespace Modules\Reports\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductsReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_products_report(): void
    {
        Permission::query()->create(['name' => 'reports.products.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reports.products.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/products-report')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_products_report(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/products-report')->assertForbidden();
    }
}
