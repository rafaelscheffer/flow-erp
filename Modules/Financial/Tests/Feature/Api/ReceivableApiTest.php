<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Models\Customer;
use Modules\Financial\Models\Receivable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReceivableApiTest extends TestCase
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

    public function test_listing_receivables_without_a_token_is_unauthorized(): void
    {
        $this->getJson('/api/v1/receivables')->assertUnauthorized();
    }

    public function test_user_can_create_and_update_a_pending_receivable(): void
    {
        $user = $this->userWithPermissions(['receivables.create', 'receivables.update']);
        $token = $user->createToken('phpunit', ['receivables.create', 'receivables.update'])->plainTextToken;
        $customer = Customer::factory()->create();

        $created = $this->withToken($token)->postJson('/api/v1/receivables', [
            'customer_id' => $customer->id,
            'amount' => 150.5,
            'due_date' => now()->addDays(10)->toDateString(),
        ])->assertCreated()->assertJsonPath('data.status', 'pending')->json('data');

        $this->withToken($token)->putJson("/api/v1/receivables/{$created['id']}", ['amount' => 200])
            ->assertOk()
            ->assertJsonPath('data.amount', '200.00');
    }

    public function test_a_paid_receivable_cannot_be_updated(): void
    {
        $user = $this->userWithPermissions(['receivables.update']);
        $token = $user->createToken('phpunit', ['receivables.update'])->plainTextToken;
        $receivable = Receivable::factory()->paid()->create();

        $this->withToken($token)->putJson("/api/v1/receivables/{$receivable->id}", ['amount' => 10])
            ->assertUnprocessable();
    }

    public function test_marking_a_pending_receivable_as_paid(): void
    {
        $user = $this->userWithPermissions(['receivables.mark-paid']);
        $token = $user->createToken('phpunit', ['receivables.mark-paid'])->plainTextToken;
        $receivable = Receivable::factory()->create();

        $this->withToken($token)->postJson("/api/v1/receivables/{$receivable->id}/mark-as-paid", ['payment_method' => 'pix'])
            ->assertOk()
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('data.payment_method', 'pix');
    }

    public function test_marking_an_already_paid_receivable_as_paid_fails(): void
    {
        $user = $this->userWithPermissions(['receivables.mark-paid']);
        $token = $user->createToken('phpunit', ['receivables.mark-paid'])->plainTextToken;
        $receivable = Receivable::factory()->paid()->create();

        $this->withToken($token)->postJson("/api/v1/receivables/{$receivable->id}/mark-as-paid", ['payment_method' => 'pix'])
            ->assertUnprocessable();
    }
}
