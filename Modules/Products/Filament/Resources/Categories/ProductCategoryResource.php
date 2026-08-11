<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Categories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Products\Filament\Resources\Categories\Pages\CreateProductCategory;
use Modules\Products\Filament\Resources\Categories\Pages\EditProductCategory;
use Modules\Products\Filament\Resources\Categories\Pages\ListProductCategories;
use Modules\Products\Filament\Resources\Categories\Schemas\ProductCategoryForm;
use Modules\Products\Filament\Resources\Categories\Tables\ProductCategoriesTable;
use Modules\Products\Models\ProductCategory;
use UnitEnum;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $slug = 'product-categories';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static UnitEnum|string|null $navigationGroup = 'Produtos';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Categoria';

    protected static ?string $pluralModelLabel = 'Categorias';

    public static function form(Schema $schema): Schema
    {
        return ProductCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductCategoriesTable::configure($table);
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
            'index' => ListProductCategories::route('/'),
            'create' => CreateProductCategory::route('/create'),
            'edit' => EditProductCategory::route('/{record}/edit'),
        ];
    }
}
