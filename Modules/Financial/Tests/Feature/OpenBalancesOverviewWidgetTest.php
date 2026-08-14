<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Financial\Filament\Widgets\OpenBalancesOverview;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OpenBalancesOverviewWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_only_receivables_permission_sees_only_the_receivables_stats(): void
    {
        Permission::query()->create(['name' => 'receivables.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('receivables.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        Receivable::factory()->create();

        $this->actingAs($user);

        Livewire::test(OpenBalancesOverview::class)
            ->assertSee('A Receber (em aberto)')
            ->assertDontSee('A Pagar (em aberto)');
    }

    public function test_user_with_only_payables_permission_sees_only_the_payables_stats(): void
    {
        Permission::query()->create(['name' => 'payables.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('payables.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        Payable::factory()->create();

        $this->actingAs($user);

        Livewire::test(OpenBalancesOverview::class)
            ->assertSee('A Pagar (em aberto)')
            ->assertDontSee('A Receber (em aberto)');
    }

    public function test_user_without_permission_cannot_view_the_widget(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertFalse(OpenBalancesOverview::canView());
    }
}
