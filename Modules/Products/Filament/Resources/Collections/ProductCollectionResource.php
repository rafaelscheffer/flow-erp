<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Collections;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Products\Filament\Resources\Collections\Pages\CreateProductCollection;
use Modules\Products\Filament\Resources\Collections\Pages\EditProductCollection;
use Modules\Products\Filament\Resources\Collections\Pages\ListProductCollections;
use Modules\Products\Filament\Resources\Collections\Schemas\ProductCollectionForm;
use Modules\Products\Filament\Resources\Collections\Tables\ProductCollectionsTable;
use Modules\Products\Models\ProductCollection;
use UnitEnum;

class ProductCollectionResource extends Resource
{
    protected static ?string $model = ProductCollection::class;

    protected static ?string $slug = 'product-collections';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = 'Produtos';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Collection';

    protected static ?string $pluralModelLabel = 'Collections';

    public static function form(Schema $schema): Schema
    {
        return ProductCollectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductCollectionsTable::configure($table);
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
            'index' => ListProductCollections::route('/'),
            'create' => CreateProductCollection::route('/create'),
            'edit' => EditProductCollection::route('/{record}/edit'),
        ];
    }
}
