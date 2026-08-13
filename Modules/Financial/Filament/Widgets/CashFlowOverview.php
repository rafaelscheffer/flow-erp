<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Modules\Financial\Services\CashFlowCalculator;

class CashFlowOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $start = isset($this->pageFilters['start_date'])
            ? Carbon::parse($this->pageFilters['start_date'])->startOfDay()
            : now()->startOfMonth();

        $end = isset($this->pageFilters['end_date'])
            ? Carbon::parse($this->pageFilters['end_date'])->endOfDay()
            : now()->endOfMonth();

        $summary = app(CashFlowCalculator::class)->summary($start, $end);

        return [
            Stat::make('Total Recebido', $this->money($summary['received']))
                ->color('success'),
            Stat::make('Total Pago', $this->money($summary['paid']))
                ->color('danger'),
            Stat::make('Saldo', $this->money($summary['balance']))
                ->color($summary['balance'] >= 0 ? 'success' : 'danger'),
        ];
    }

    private function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
