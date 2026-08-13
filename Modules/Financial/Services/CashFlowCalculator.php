<?php

declare(strict_types=1);

namespace Modules\Financial\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;

class CashFlowCalculator
{
    /**
     * @return array{received: float, paid: float, balance: float}
     */
    public function summary(CarbonInterface $start, CarbonInterface $end): array
    {
        $received = (float) Receivable::query()
            ->where('status', ReceivableStatus::Paid)
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');

        $paid = (float) Payable::query()
            ->where('status', PayableStatus::Paid)
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');

        return [
            'received' => $received,
            'paid' => $paid,
            'balance' => $received - $paid,
        ];
    }

    /**
     * @return list<array{month: string, received: float, paid: float}>
     */
    public function monthlyTotals(int $months = 6): array
    {
        $totals = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $reference = CarbonImmutable::now()->subMonths($i);
            $start = $reference->startOfMonth();
            $end = $reference->endOfMonth();

            $summary = $this->summary($start, $end);

            $totals[] = [
                'month' => ucfirst($reference->translatedFormat('M/y')),
                'received' => $summary['received'],
                'paid' => $summary['paid'],
            ];
        }

        return $totals;
    }
}
