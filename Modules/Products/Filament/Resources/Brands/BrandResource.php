<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Brands;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Products\Filament\Resources\Brands\Pages\CreateBrand;
use Modules\Products\Filament\Resources\Brands\Pages\EditBrand;
use Modules\Products\Filament\Resources\Brands\Pages\ListBrands;
use Modules\Products\Filament\Resources\Brands\Schemas\BrandForm;
use Modules\Products\Filament\Resources\Brands\Tables\BrandsTable;
use Modules\Products\Models\Brand;
use UnitEnum;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static UnitEnum|string|null $navigationGroup = 'Produtos';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Marca';

    protected static ?string $pluralModelLabel = 'Marcas';

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }
}
