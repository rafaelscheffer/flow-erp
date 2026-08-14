<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Receivable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CashFlowApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_flow_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/cash-flow')->assertUnauthorized();
    }

    public function test_user_with_permission_sees_the_cash_flow_summary(): void
    {
        Permission::query()->firstOrCreate(['name' => 'cash-flow.view']);
        $role = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo('cash-flow.view');

        $user = User::factory()->create();
        $user->assignRole($role);
        $token = $user->createToken('phpunit', ['cash-flow.view'])->plainTextToken;

        Receivable::factory()->paid()->create(['amount' => 100]);

        $this->withToken($token)->getJson('/api/v1/cash-flow')
            ->assertOk()
            ->assertJsonStructure(['summary' => ['received', 'paid', 'balance'], 'monthly_totals']);
    }
}
