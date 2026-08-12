<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('type')
                    ->label('Tipo')
                    ->options(collect(StockMovementType::creatableCases())->mapWithKeys(
                        fn (StockMovementType $case): array => [$case->value => $case->label()],
                    ))
                    ->live()
                    ->required(),
                Select::make('stock_location_id')
                    ->label('Local')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
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
                TextInput::make('quantity')
                    ->label('Quantidade')
                    ->numeric()
                    ->integer()
                    ->required()
                    ->rules(fn (Get $get): array => in_array($get('type'), [
                        StockMovementType::Entrada->value,
                        StockMovementType::Saida->value,
                    ], true) ? ['min:1'] : ['not_in:0'])
                    ->helperText(
                        fn (Get $get): string => in_array($get('type'), [
                            StockMovementType::Entrada->value,
                            StockMovementType::Saida->value,
                        ], true)
                            ? 'Informe a quantidade movimentada (valor positivo).'
                            : 'Use valor positivo para aumento ou negativo para redução do estoque.',
                    ),
                Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
