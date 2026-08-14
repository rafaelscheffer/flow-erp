<?php

declare(strict_types=1);

namespace Modules\Purchases\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Models\PurchaseOrder;

class PurchasesOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->can('purchase-orders.view') ?? false;
    }

    protected function getStats(): array
    {
        $ordersThisMonth = PurchaseOrder::query()
            ->with('items')
            ->whereBetween('order_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();

        $purchased = $ordersThisMonth
            ->where('status', PurchaseOrderStatus::Received)
            ->sum(fn (PurchaseOrder $order): float => $order->total);

        $awaitingReceipt = PurchaseOrder::query()->where('status', PurchaseOrderStatus::Sent)->count();

        return [
            Stat::make('Pedidos de compra no mês', (string) $ordersThisMonth->count())
                ->color('info'),
            Stat::make('Total comprado no mês', $this->money($purchased))
                ->color('success'),
            Stat::make('Aguardando recebimento', (string) $awaitingReceipt)
                ->color($awaitingReceipt > 0 ? 'warning' : 'success'),
        ];
    }

    private function money(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
