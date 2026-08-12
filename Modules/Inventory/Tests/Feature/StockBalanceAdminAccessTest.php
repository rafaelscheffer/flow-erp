<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockBalanceAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_stock_balances_resource(): void
    {
        Permission::query()->create(['name' => 'balances.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('balances.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/stock-balances')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_stock_balances_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/stock-balances')->assertForbidden();
    }
}
