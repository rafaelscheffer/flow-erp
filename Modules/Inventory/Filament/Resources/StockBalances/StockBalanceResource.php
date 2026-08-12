<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockBalances;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Inventory\Filament\Resources\StockBalances\Pages\ListStockBalances;
use Modules\Inventory\Filament\Resources\StockBalances\Tables\StockBalancesTable;
use Modules\Inventory\Models\StockBalance;
use UnitEnum;

class StockBalanceResource extends Resource
{
    protected static ?string $model = StockBalance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static UnitEnum|string|null $navigationGroup = 'Estoque';

    protected static ?string $navigationLabel = 'Saldo de Estoque';

    protected static ?string $modelLabel = 'Saldo de Estoque';

    protected static ?string $pluralModelLabel = 'Saldos de Estoque';

    public static function table(Table $table): Table
    {
        return StockBalancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockBalances::route('/'),
        ];
    }
}
