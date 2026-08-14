<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockMovement;

class InventoryExporter extends BaseExporter
{
    protected static ?string $model = StockMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')->label('Data'),
            ExportColumn::make('type')
                ->label('Tipo')
                ->formatStateUsing(fn (StockMovementType $state): string => $state->label()),
            ExportColumn::make('product.name')->label('Produto'),
            ExportColumn::make('variant.sku')->label('Variante'),
            ExportColumn::make('location.name')->label('Local'),
            ExportColumn::make('quantity')->label('Quantidade'),
            ExportColumn::make('performedBy.name')->label('Responsável'),
        ];
    }
}
