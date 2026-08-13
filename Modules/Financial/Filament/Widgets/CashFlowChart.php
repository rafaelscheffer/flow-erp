<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Financial\Services\CashFlowCalculator;

class CashFlowChart extends ChartWidget
{
    protected ?string $heading = 'Entradas x Saídas (últimos 6 meses)';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $totals = app(CashFlowCalculator::class)->monthlyTotals();

        return [
            'datasets' => [
                [
                    'label' => 'Recebido',
                    'data' => array_column($totals, 'received'),
                    'backgroundColor' => '#22c55e',
                ],
                [
                    'label' => 'Pago',
                    'data' => array_column($totals, 'paid'),
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => array_column($totals, 'month'),
        ];
    }
}
