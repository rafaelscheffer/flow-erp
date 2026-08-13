<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\CostCenters;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Financial\Filament\Resources\CostCenters\Pages\CreateCostCenter;
use Modules\Financial\Filament\Resources\CostCenters\Pages\EditCostCenter;
use Modules\Financial\Filament\Resources\CostCenters\Pages\ListCostCenters;
use Modules\Financial\Filament\Resources\CostCenters\Schemas\CostCenterForm;
use Modules\Financial\Filament\Resources\CostCenters\Tables\CostCentersTable;
use Modules\Financial\Models\CostCenter;
use UnitEnum;

class CostCenterResource extends Resource
{
    protected static ?string $model = CostCenter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Centros de Custo';

    protected static ?string $modelLabel = 'Centro de Custo';

    protected static ?string $pluralModelLabel = 'Centros de Custo';

    public static function form(Schema $schema): Schema
    {
        return CostCenterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CostCentersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCostCenters::route('/'),
            'create' => CreateCostCenter::route('/create'),
            'edit' => EditCostCenter::route('/{record}/edit'),
        ];
    }
}
