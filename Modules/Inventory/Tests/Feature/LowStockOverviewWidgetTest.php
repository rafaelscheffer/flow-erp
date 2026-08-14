<?php

declare(strict_types=1);

namespace Modules\Inventory\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Inventory\Filament\Widgets\LowStockOverview;
use Modules\Inventory\Models\StockBalance;
use Modules\Inventory\Models\StockLocation;
use Modules\Products\Models\Product;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LowStockOverviewWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_sees_low_stock_products(): void
    {
        Permission::query()->create(['name' => 'balances.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('balances.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $location = StockLocation::factory()->create();
        $product = Product::factory()->create(['min_stock' => 5]);

        StockBalance::query()->create([
            'product_id' => $product->id,
            'stock_location_id' => $location->id,
            'quantity' => 2,
            'reserved_quantity' => 0,
        ]);

        $this->actingAs($user);

        Livewire::test(LowStockOverview::class)
            ->assertSee($product->name);
    }

    public function test_user_without_permission_cannot_view_the_widget(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertFalse(LowStockOverview::canView());
    }
}
