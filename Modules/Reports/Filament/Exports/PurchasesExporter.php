<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Models\PurchaseOrder;

class PurchasesExporter extends BaseExporter
{
    protected static ?string $model = PurchaseOrder::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('Nº'),
            ExportColumn::make('supplier.name')->label('Fornecedor'),
            ExportColumn::make('order_date')->label('Data do Pedido'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (PurchaseOrderStatus $state): string => $state->label()),
            ExportColumn::make('total')
                ->label('Total')
                ->state(fn (PurchaseOrder $record): float => $record->total),
        ];
    }
}
