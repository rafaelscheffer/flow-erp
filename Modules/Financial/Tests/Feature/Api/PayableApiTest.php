<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Financial\Models\Payable;
use Modules\Purchases\Models\Supplier;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayableApiTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermissions(array $permissions): User
    {
        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $role = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo($permissions);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_listing_payables_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/payables')->assertUnauthorized();
    }

    public function test_user_can_create_and_update_a_pending_payable(): void
    {
        $user = $this->userWithPermissions(['payables.create', 'payables.update']);
        $token = $user->createToken('phpunit', ['payables.create', 'payables.update'])->plainTextToken;
        $supplier = Supplier::factory()->create();

        $created = $this->withToken($token)->postJson('/api/v1/payables', [
            'supplier_id' => $supplier->id,
            'amount' => 300,
            'due_date' => now()->addDays(15)->toDateString(),
        ])->assertCreated()->assertJsonPath('data.status', 'pending')->json('data');

        $this->withToken($token)->putJson("/api/v1/payables/{$created['id']}", ['amount' => 350])
            ->assertOk()
            ->assertJsonPath('data.amount', '350.00');
    }

    public function test_a_paid_payable_cannot_be_deleted(): void
    {
        $user = $this->userWithPermissions(['payables.delete']);
        $token = $user->createToken('phpunit', ['payables.delete'])->plainTextToken;
        $payable = Payable::factory()->paid()->create();

        $this->withToken($token)->deleteJson("/api/v1/payables/{$payable->id}")->assertUnprocessable();
    }

    public function test_marking_a_pending_payable_as_paid(): void
    {
        $user = $this->userWithPermissions(['payables.mark-paid']);
        $token = $user->createToken('phpunit', ['payables.mark-paid'])->plainTextToken;
        $payable = Payable::factory()->create();

        $this->withToken($token)->postJson("/api/v1/payables/{$payable->id}/mark-as-paid", ['payment_method' => 'bank_slip'])
            ->assertOk()
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('data.payment_method', 'bank_slip');
    }
}
