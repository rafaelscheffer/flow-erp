<?php

declare(strict_types=1);

namespace Modules\Reports\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReceivablesReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_receivables_report(): void
    {
        Permission::query()->create(['name' => 'reports.receivables.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reports.receivables.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/receivables-report')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_receivables_report(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/receivables-report')->assertForbidden();
    }
}
