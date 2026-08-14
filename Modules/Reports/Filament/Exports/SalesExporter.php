<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Enums\PaymentMethod;
use Modules\Sales\Models\Order;

class SalesExporter extends BaseExporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('Nº'),
            ExportColumn::make('customer.name')->label('Cliente'),
            ExportColumn::make('order_date')->label('Data do Pedido'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (OrderStatus $state): string => $state->label()),
            ExportColumn::make('payment_method')
                ->label('Forma de Pagamento')
                ->formatStateUsing(fn (PaymentMethod $state): string => $state->label()),
            ExportColumn::make('discount_amount')->label('Desconto'),
            ExportColumn::make('shipping_amount')->label('Frete'),
            ExportColumn::make('total')
                ->label('Total')
                ->state(fn (Order $record): float => $record->total),
        ];
    }
}
