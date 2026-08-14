<?php

declare(strict_types=1);

namespace Modules\Reports\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayablesReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_payables_report(): void
    {
        Permission::query()->create(['name' => 'reports.payables.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reports.payables.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/payables-report')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_payables_report(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/payables-report')->assertForbidden();
    }
}
