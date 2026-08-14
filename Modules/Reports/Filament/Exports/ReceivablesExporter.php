<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Receivable;

class ReceivablesExporter extends BaseExporter
{
    protected static ?string $model = Receivable::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('customer.name')->label('Cliente'),
            ExportColumn::make('order_id')->label('Pedido'),
            ExportColumn::make('amount')->label('Valor'),
            ExportColumn::make('due_date')->label('Vencimento'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (ReceivableStatus $state): string => $state->label()),
            ExportColumn::make('paid_at')->label('Pago em'),
        ];
    }
}
