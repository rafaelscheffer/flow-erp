<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Modules\Products\Models\Product;

class LowStockOverview extends TableWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->can('balances.view') ?? false;
    }

    public function table(Table $table): Table
    {
        $stockQuantitySubQuery = 'select coalesce(sum(quantity), 0) from stock_balances where stock_balances.product_id = products.id';

        return $table
            ->heading('Produtos com Estoque Baixo')
            ->query(
                Product::query()
                    ->select('products.*')
                    ->selectRaw("({$stockQuantitySubQuery}) as stock_quantity")
                    ->where('is_active', true)
                    ->where('min_stock', '>', 0)
                    ->whereRaw("({$stockQuantitySubQuery}) <= products.min_stock")
            )
            ->columns([
                TextColumn::make('name')->label('Produto'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('stock_quantity')->label('Estoque Atual')->color('danger'),
                TextColumn::make('min_stock')->label('Estoque Mínimo'),
            ])
            ->defaultSort('stock_quantity')
            ->paginated([5, 10, 25]);
    }
}
