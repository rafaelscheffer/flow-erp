<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockReservations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;

class StockReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('product_id')
                    ->label('Produto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->afterStateUpdated(fn (callable $set) => $set('product_variant_id', null)),
                Select::make('product_variant_id')
                    ->label('Variante')
                    ->options(
                        fn (Get $get): array => $get('product_id')
                            ? ProductVariant::query()->where('product_id', $get('product_id'))->pluck('sku', 'id')->toArray()
                            : [],
                    )
                    ->searchable()
                    ->visible(
                        fn (Get $get): bool => Product::query()->find($get('product_id'))?->type === ProductType::Variable,
                    ),
                Select::make('stock_location_id')
                    ->label('Local')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->label('Quantidade')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->required(),
                Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
