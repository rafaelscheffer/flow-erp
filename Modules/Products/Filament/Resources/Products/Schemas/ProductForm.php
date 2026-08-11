<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Products\Enums\ProductType;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação')
                    ->columns(2)
                    ->schema([
                        Radio::make('type')
                            ->label('Tipo')
                            ->options(collect(ProductType::cases())->mapWithKeys(
                                fn (ProductType $case): array => [$case->value => $case->label()],
                            ))
                            ->default(ProductType::Simple->value)
                            ->live()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('product_category_id')
                            ->label('Categoria')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('brand_id')
                            ->label('Marca')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('product_collection_id')
                            ->label('Coleção')
                            ->relationship('collection', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('internal_code')
                            ->label('Código Interno')
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Códigos')
                    ->columns(3)
                    ->visible(fn (Get $get): bool => $get('type') === ProductType::Simple->value)
                    ->schema([
                        TextInput::make('sku')
                            ->label('SKU')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('ean')
                            ->label('EAN')
                            ->unique(ignoreRecord: true)
                            ->rules(['regex:/^\d{8}(\d{4,6})?$/'])
                            ->maxLength(14),
                        TextInput::make('ncm')
                            ->label('NCM')
                            ->rules(['regex:/^\d{8}$/'])
                            ->maxLength(8),
                    ]),
                Section::make('Preços')
                    ->columns(3)
                    ->schema([
                        TextInput::make('cost_price')
                            ->label('Preço de Custo')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),
                        TextInput::make('sale_price')
                            ->label('Preço de Venda')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),
                        TextInput::make('promotional_price')
                            ->label('Preço Promocional')
                            ->numeric()
                            ->prefix('R$'),
                    ]),
                Section::make('Peso e Dimensões')
                    ->columns(4)
                    ->schema([
                        TextInput::make('weight')
                            ->label('Peso')
                            ->numeric()
                            ->suffix('kg'),
                        TextInput::make('height')
                            ->label('Altura')
                            ->numeric()
                            ->suffix('cm'),
                        TextInput::make('width')
                            ->label('Largura')
                            ->numeric()
                            ->suffix('cm'),
                        TextInput::make('length')
                            ->label('Profundidade')
                            ->numeric()
                            ->suffix('cm'),
                    ]),
                Section::make('Controle de Estoque')
                    ->columns(2)
                    ->schema([
                        TextInput::make('min_stock')
                            ->label('Estoque Mínimo')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('max_stock')
                            ->label('Estoque Máximo')
                            ->numeric(),
                    ]),
                Section::make('Fotos')
                    ->schema([
                        Repeater::make('photos')
                            ->relationship('photos')
                            ->label('Fotos')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Imagem')
                                    ->image()
                                    ->disk('public')
                                    ->directory('products')
                                    ->required(),
                                Toggle::make('is_primary')
                                    ->label('Principal')
                                    ->default(false),
                            ])
                            ->columns(2)
                            ->orderColumn('sort_order')
                            ->addActionLabel('Adicionar Foto')
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
