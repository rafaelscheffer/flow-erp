<?php

declare(strict_types=1);

namespace Modules\Reports\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomersReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_customers_report(): void
    {
        Permission::query()->create(['name' => 'reports.customers.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('reports.customers.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/customers-report')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_customers_report(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/customers-report')->assertForbidden();
    }
}
