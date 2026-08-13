<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CashFlowPageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_cash_flow_page(): void
    {
        Permission::query()->create(['name' => 'cash-flow.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('cash-flow.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/cash-flow')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_cash_flow_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/cash-flow')->assertForbidden();
    }
}
