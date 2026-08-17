<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Feature;

use App\Mail\OverdueFinancialsDigest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Jobs\SendOverdueFinancialsDigestJob;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendOverdueFinancialsDigestJobTest extends TestCase
{
    use RefreshDatabase;

    private function createRecipient(): User
    {
        Permission::query()->firstOrCreate(['name' => 'receivables.view']);
        Permission::query()->firstOrCreate(['name' => 'payables.view']);
        $role = Role::query()->firstOrCreate(['name' => 'Financeiro']);
        $role->givePermissionTo(['receivables.view', 'payables.view']);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_sends_a_digest_to_users_with_financial_permissions_when_there_are_overdue_items(): void
    {
        Mail::fake();

        $recipient = $this->createRecipient();

        $receivable = Receivable::factory()->create([
            'status' => ReceivableStatus::Pending,
            'due_date' => now()->subDays(5),
        ]);

        $payable = Payable::factory()->create([
            'status' => PayableStatus::Pending,
            'due_date' => now()->subDays(2),
        ]);

        (new SendOverdueFinancialsDigestJob)->handle();

        Mail::assertQueued(
            OverdueFinancialsDigest::class,
            fn (OverdueFinancialsDigest $mail): bool => $mail->hasTo($recipient->email)
                && $mail->overdueReceivables->contains($receivable)
                && $mail->overduePayables->contains($payable),
        );
    }

    public function test_does_not_send_anything_when_there_are_no_overdue_items(): void
    {
        Mail::fake();

        $this->createRecipient();

        Receivable::factory()->create([
            'status' => ReceivableStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        (new SendOverdueFinancialsDigestJob)->handle();

        Mail::assertNothingSent();
    }

    public function test_does_not_send_to_users_without_financial_permissions(): void
    {
        Mail::fake();

        Permission::query()->firstOrCreate(['name' => 'receivables.view']);
        Permission::query()->firstOrCreate(['name' => 'payables.view']);
        User::factory()->create();

        Receivable::factory()->create([
            'status' => ReceivableStatus::Pending,
            'due_date' => now()->subDays(5),
        ]);

        (new SendOverdueFinancialsDigestJob)->handle();

        Mail::assertNothingSent();
    }

    public function test_the_digest_job_is_scheduled_daily(): void
    {
        $this->artisan('schedule:list')
            ->assertSuccessful()
            ->expectsOutputToContain('SendOverdueFinancialsDigestJob');
    }
}
