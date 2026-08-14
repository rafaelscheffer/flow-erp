<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;

class ProductsExporter extends BaseExporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('sku')->label('SKU'),
            ExportColumn::make('name')->label('Nome'),
            ExportColumn::make('category.name')->label('Categoria'),
            ExportColumn::make('brand.name')->label('Marca'),
            ExportColumn::make('type')
                ->label('Tipo')
                ->formatStateUsing(fn (ProductType $state): string => $state->label()),
            ExportColumn::make('cost_price')->label('Preço de Custo'),
            ExportColumn::make('sale_price')->label('Preço de Venda'),
            ExportColumn::make('min_stock')->label('Estoque Mínimo'),
            ExportColumn::make('max_stock')->label('Estoque Máximo'),
            ExportColumn::make('is_active')->label('Ativo'),
        ];
    }
}
