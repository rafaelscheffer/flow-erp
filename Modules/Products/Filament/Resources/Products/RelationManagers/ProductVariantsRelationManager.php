<?php

declare(strict_types=1);

namespace Modules\Products\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255),
                TextInput::make('ean')
                    ->label('EAN')
                    ->maxLength(14),
                TextInput::make('color')
                    ->label('Cor')
                    ->maxLength(255),
                TextInput::make('size')
                    ->label('Tamanho')
                    ->maxLength(255),
                TextInput::make('cost_price')
                    ->label('Preço de Custo')
                    ->numeric()
                    ->prefix('R$'),
                TextInput::make('sale_price')
                    ->label('Preço de Venda')
                    ->numeric()
                    ->prefix('R$'),
                TextInput::make('promotional_price')
                    ->label('Preço Promocional')
                    ->numeric()
                    ->prefix('R$'),
                TextInput::make('weight')
                    ->label('Peso')
                    ->numeric()
                    ->suffix('kg'),
                Toggle::make('is_active')
                    ->label('Ativo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('ean')
                    ->label('EAN')
                    ->placeholder('—'),
                TextColumn::make('color')
                    ->label('Cor')
                    ->placeholder('—'),
                TextColumn::make('size')
                    ->label('Tamanho')
                    ->placeholder('—'),
                TextColumn::make('sale_price')
                    ->label('Preço de Venda')
                    ->money('BRL')
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
