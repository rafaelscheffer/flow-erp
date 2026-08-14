<?php

declare(strict_types=1);

namespace Modules\Purchases\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Purchases\Filament\Widgets\PurchasesOverview;
use Modules\Purchases\Models\PurchaseOrder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PurchasesOverviewWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_sees_the_purchases_overview_widget(): void
    {
        Permission::query()->create(['name' => 'purchase-orders.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('purchase-orders.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        PurchaseOrder::factory()->received()->create(['order_date' => now()]);

        $this->actingAs($user);

        Livewire::test(PurchasesOverview::class)
            ->assertSee('Pedidos de compra no mês');
    }

    public function test_user_without_permission_cannot_view_the_purchases_overview_widget(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertFalse(PurchasesOverview::canView());
    }
}
