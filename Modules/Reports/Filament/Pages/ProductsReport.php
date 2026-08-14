<?php

declare(strict_types=1);

namespace Modules\Reports\Filament\Pages;

use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;
use Modules\Reports\Filament\Exports\ProductsExporter;
use UnitEnum;

class ProductsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    protected static UnitEnum|string|null $navigationGroup = 'Relatórios';

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $title = 'Relatório de Produtos';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('reports.products.view') ?? false;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->with(['category', 'brand']))
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Categoria'),
                TextColumn::make('brand.name')
                    ->label('Marca'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (ProductType $state): string => $state->label()),
                TextColumn::make('cost_price')
                    ->label('Custo')
                    ->money('BRL'),
                TextColumn::make('sale_price')
                    ->label('Venda')
                    ->money('BRL'),
                TextColumn::make('min_stock')
                    ->label('Estoque Mín.'),
                TextColumn::make('max_stock')
                    ->label('Estoque Máx.'),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('product_category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(collect(ProductType::cases())->mapWithKeys(
                        fn (ProductType $case): array => [$case->value => $case->label()],
                    )),
                TernaryFilter::make('is_active')
                    ->label('Ativo'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar')
                    ->exporter(ProductsExporter::class),
            ])
            ->defaultSort('name');
    }
}
