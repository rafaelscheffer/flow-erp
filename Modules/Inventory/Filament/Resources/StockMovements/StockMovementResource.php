<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockMovements;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Inventory\Filament\Resources\StockMovements\Pages\CreateStockMovement;
use Modules\Inventory\Filament\Resources\StockMovements\Pages\ListStockMovements;
use Modules\Inventory\Filament\Resources\StockMovements\Schemas\StockMovementForm;
use Modules\Inventory\Filament\Resources\StockMovements\Tables\StockMovementsTable;
use Modules\Inventory\Models\StockMovement;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static UnitEnum|string|null $navigationGroup = 'Estoque';

    protected static ?string $navigationLabel = 'Movimentações';

    public static function form(Schema $schema): Schema
    {
        return StockMovementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
        ];
    }
}
