<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Receivable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReceivableAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_view_the_receivables_resource(): void
    {
        Permission::query()->create(['name' => 'receivables.view']);
        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo('receivables.view');

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/receivables')->assertOk();
    }

    public function test_user_without_permission_cannot_view_the_receivables_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/receivables')->assertForbidden();
    }

    public function test_user_with_permission_can_create_a_receivable(): void
    {
        foreach (['receivables.view', 'receivables.create'] as $permission) {
            Permission::query()->create(['name' => $permission]);
        }

        $role = Role::query()->create(['name' => 'Super Admin']);
        $role->givePermissionTo(['receivables.view', 'receivables.create']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get('/admin/receivables/create')->assertOk();
    }

    public function test_creating_a_receivable_records_an_audit_entry(): void
    {
        $receivable = Receivable::factory()->create();

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Receivable::class,
            'auditable_id' => $receivable->id,
            'event' => 'created',
        ]);
    }
}
