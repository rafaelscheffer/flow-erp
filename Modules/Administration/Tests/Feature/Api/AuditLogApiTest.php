<?php

declare(strict_types=1);

namespace Modules\Administration\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Models\Customer;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_audit_logs_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/audit-logs')->assertUnauthorized();
    }

    public function test_user_with_permission_can_list_audit_logs(): void
    {
        Permission::query()->firstOrCreate(['name' => 'audit-logs.view']);
        $role = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo('audit-logs.view');

        $user = User::factory()->create();
        $user->assignRole($role);
        $token = $user->createToken('phpunit', ['audit-logs.view'])->plainTextToken;

        Customer::factory()->create();

        $this->withToken($token)->getJson('/api/v1/audit-logs')
            ->assertOk()
            ->assertJsonPath('data.0.event', 'created');
    }

    public function test_audit_logs_have_no_write_routes(): void
    {
        Permission::query()->firstOrCreate(['name' => 'audit-logs.view']);
        $role = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo('audit-logs.view');

        $user = User::factory()->create();
        $user->assignRole($role);
        $token = $user->createToken('phpunit', ['audit-logs.view'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/audit-logs', [])->assertMethodNotAllowed();
    }
}
