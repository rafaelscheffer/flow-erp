<?php

declare(strict_types=1);

namespace Modules\Financial\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;
use Modules\Financial\Services\CashFlowCalculator;
use Tests\TestCase;

class CashFlowCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_only_sums_paid_records_within_the_given_range(): void
    {
        Receivable::factory()->paid()->create(['amount' => 100, 'paid_at' => Carbon::parse('2026-08-05')]);
        Receivable::factory()->paid()->create(['amount' => 50, 'paid_at' => Carbon::parse('2026-07-05')]);
        Receivable::factory()->create(['amount' => 999]);

        Payable::factory()->paid()->create(['amount' => 40, 'paid_at' => Carbon::parse('2026-08-10')]);
        Payable::factory()->create(['amount' => 500]);

        $summary = (new CashFlowCalculator)->summary(
            Carbon::parse('2026-08-01')->startOfDay(),
            Carbon::parse('2026-08-31')->endOfDay(),
        );

        $this->assertSame(100.0, $summary['received']);
        $this->assertSame(40.0, $summary['paid']);
        $this->assertSame(60.0, $summary['balance']);
    }

    public function test_monthly_totals_buckets_the_current_month_correctly(): void
    {
        Receivable::factory()->paid()->create([
            'amount' => 250,
            'paid_at' => now()->startOfMonth()->addDays(2),
        ]);
        Payable::factory()->paid()->create([
            'amount' => 90,
            'paid_at' => now()->startOfMonth()->addDays(3),
        ]);

        $totals = (new CashFlowCalculator)->monthlyTotals(1);

        $this->assertCount(1, $totals);
        $this->assertSame(250.0, $totals[0]['received']);
        $this->assertSame(90.0, $totals[0]['paid']);
    }
}
