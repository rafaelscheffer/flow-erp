<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockReservations;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Inventory\Filament\Resources\StockReservations\Pages\CreateStockReservation;
use Modules\Inventory\Filament\Resources\StockReservations\Pages\ListStockReservations;
use Modules\Inventory\Filament\Resources\StockReservations\Schemas\StockReservationForm;
use Modules\Inventory\Filament\Resources\StockReservations\Tables\StockReservationsTable;
use Modules\Inventory\Models\StockReservation;
use UnitEnum;

class StockReservationResource extends Resource
{
    protected static ?string $model = StockReservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmark;

    protected static UnitEnum|string|null $navigationGroup = 'Estoque';

    protected static ?string $navigationLabel = 'Reservas de Estoque';

    public static function form(Schema $schema): Schema
    {
        return StockReservationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockReservationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockReservations::route('/'),
            'create' => CreateStockReservation::route('/create'),
        ];
    }
}
