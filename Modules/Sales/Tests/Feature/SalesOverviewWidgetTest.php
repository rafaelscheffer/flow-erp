<?php

declare(strict_types=1);

namespace Modules\Sales\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Sales\Filament\Widgets\SalesOverview;
use Modules\Sales\Models\Order;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesOverviewWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_sees_the_sales_overview_widget(): void
    {
        Permission::query()->create(['name' => 'orders.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('orders.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        Order::factory()->confirmed()->create(['order_date' => now()]);

        $this->actingAs($user);

        Livewire::test(SalesOverview::class)
            ->assertSee('Pedidos no mês');
    }

    public function test_user_without_permission_cannot_view_the_sales_overview_widget(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertFalse(SalesOverview::canView());
    }
}
