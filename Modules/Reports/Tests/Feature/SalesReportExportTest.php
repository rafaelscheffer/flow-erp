<?php

declare(strict_types=1);

namespace Modules\Reports\Tests\Feature;

use App\Models\User;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Reports\Filament\Pages\SalesReport;
use Modules\Sales\Models\Order;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_exporting_the_sales_report_completes_and_covers_all_orders(): void
    {
        Permission::query()->create(['name' => 'reports.sales.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reports.sales.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        Order::factory()->count(3)->create();

        $this->actingAs($user);

        Livewire::test(SalesReport::class)
            ->callTableAction('export')
            ->assertHasNoTableActionErrors();

        $export = Export::query()->latest('id')->first();

        $this->assertNotNull($export);
        $this->assertSame(3, $export->total_rows);
        $this->assertNotNull($export->completed_at);
        $this->assertSame(3, $export->successful_rows);
    }
}
