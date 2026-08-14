<?php

declare(strict_types=1);

namespace Modules\Sales\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Models\Order;

class SalesOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->can('orders.view') ?? false;
    }

    protected function getStats(): array
    {
        $ordersThisMonth = Order::query()
            ->with('items')
            ->whereBetween('order_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();

        $revenue = $ordersThisMonth
            ->whereIn('status', [OrderStatus::Confirmed, OrderStatus::Invoiced])
            ->sum(fn (Order $order): float => $order->total);

        $openDrafts = Order::query()->where('status', OrderStatus::Draft)->count();

        return [
            Stat::make('Pedidos no mês', (string) $ordersThisMonth->count())
                ->color('info'),
            Stat::make('Faturamento no mês', $this->money($revenue))
                ->color('success'),
            Stat::make('Rascunhos em aberto', (string) $openDrafts)
                ->color($openDrafts > 0 ? 'warning' : 'success'),
        ];
    }

    private function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
