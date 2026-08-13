<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Account;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_accounts_resource(): void
    {
        Permission::query()->create(['name' => 'accounts.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('accounts.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/accounts')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_accounts_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/accounts')->assertForbidden();
    }

    public function test_user_with_permission_can_create_an_account(): void
    {
        foreach (['accounts.view', 'accounts.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['accounts.view', 'accounts.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/accounts/create')->assertOk();
    }

    public function test_creating_an_account_records_an_audit_entry(): void
    {
        $account = Account::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Account::class,
            'auditable_id' => $account->id,
            'event' => 'created',
        ]);
    }
}
