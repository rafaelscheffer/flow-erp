<?php

declare(strict_types=1);

namespace Modules\Reports\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sales\Models\Order;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_sales_report(): void
    {
        Permission::query()->create(['name' => 'reports.sales.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reports.sales.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $order = Order::factory()->create();

        $this->actingAs($user)
            ->get('/admin/sales-report')
            ->assertOk()
            ->assertSee($order->customer->name);
    }

    public function test_user_without_permission_cannot_view_the_sales_report(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/sales-report')->assertForbidden();
    }
}
