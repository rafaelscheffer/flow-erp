<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Payable;
use Modules\Financial\Models\Receivable;

class OpenBalancesOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        $user = Auth::user();

        return ($user?->can('receivables.view') ?? false) || ($user?->can('payables.view') ?? false);
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $stats = [];

        if ($user?->can('receivables.view')) {
            $pending = Receivable::query()->where('status', ReceivableStatus::Pending);
            $overdue = (clone $pending)->where('due_date', '<', today());

            $overdueCount = $overdue->count();

            $stats[] = Stat::make('A Receber (em aberto)', $this->money((float) $pending->sum('amount')))
                ->color('success');

            $stats[] = Stat::make('Recebíveis Vencidos', (string) $overdueCount)
                ->description($this->money((float) $overdue->sum('amount')))
                ->color($overdueCount > 0 ? 'danger' : 'success');
        }

        if ($user?->can('payables.view')) {
            $pending = Payable::query()->where('status', PayableStatus::Pending);
            $overdue = (clone $pending)->where('due_date', '<', today());

            $overdueCount = $overdue->count();

            $stats[] = Stat::make('A Pagar (em aberto)', $this->money((float) $pending->sum('amount')))
                ->color('danger');

            $stats[] = Stat::make('Pagáveis Vencidos', (string) $overdueCount)
                ->description($this->money((float) $overdue->sum('amount')))
                ->color($overdueCount > 0 ? 'danger' : 'success');
        }

        return $stats;
    }

    private function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
