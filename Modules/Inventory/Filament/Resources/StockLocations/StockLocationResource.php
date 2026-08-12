<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockLocations;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Inventory\Filament\Resources\StockLocations\Pages\CreateStockLocation;
use Modules\Inventory\Filament\Resources\StockLocations\Pages\EditStockLocation;
use Modules\Inventory\Filament\Resources\StockLocations\Pages\ListStockLocations;
use Modules\Inventory\Filament\Resources\StockLocations\Schemas\StockLocationForm;
use Modules\Inventory\Filament\Resources\StockLocations\Tables\StockLocationsTable;
use Modules\Inventory\Models\StockLocation;
use UnitEnum;

class StockLocationResource extends Resource
{
    protected static ?string $model = StockLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static UnitEnum|string|null $navigationGroup = 'Estoque';

    protected static ?string $navigationLabel = 'Locais de Estoque';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StockLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockLocationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockLocations::route('/'),
            'create' => CreateStockLocation::route('/create'),
            'edit' => EditStockLocation::route('/{record}/edit'),
        ];
    }
}
