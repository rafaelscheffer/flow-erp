<?php

declare(strict_types=1);

namespace Modules\Administration\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_guest_is_redirected_away_from_the_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_user_with_permission_can_view_the_users_resource(): void
    {
        Permission::query()->create(['name' => 'users.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('users.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/users')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_users_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_super_admin_can_access_roles_audit_log_and_profile_pages(): void
    {
        foreach (['users.view', 'roles.view', 'audit-logs.view'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['users.view', 'roles.view', 'audit-logs.view']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/roles')->assertOk();
        $this->actingAs($user)->get('/admin/audit-logs')->assertOk();
        $this->actingAs($user)->get('/admin/profile')->assertOk();
    }
}
