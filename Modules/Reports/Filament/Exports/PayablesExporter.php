<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Models\Payable;

class PayablesExporter extends BaseExporter
{
    protected static ?string $model = Payable::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('supplier.name')->label('Fornecedor'),
            ExportColumn::make('purchase_order_id')->label('Pedido de Compra'),
            ExportColumn::make('amount')->label('Valor'),
            ExportColumn::make('due_date')->label('Vencimento'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (PayableStatus $state): string => $state->label()),
            ExportColumn::make('paid_at')->label('Pago em'),
        ];
    }
}
