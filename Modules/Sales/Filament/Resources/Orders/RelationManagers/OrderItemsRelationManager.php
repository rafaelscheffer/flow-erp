<?php

declare(strict_types=1);

namespace Modules\Sales\Filament\Resources\Orders\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Products\Enums\ProductType;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductVariant;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Models\Order;
use Modules\Sales\Models\OrderItem;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Itens do Pedido';

    public function form(Schema $schema): Schema
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
                    ->afterStateUpdated(function (callable $set, ?string $state): void {
                        $set('product_variant_id', null);
                        $set('unit_price', $state ? Product::query()->find($state)?->sale_price : null);
                    }),
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
                    ->minValue(1)
                    ->required(),
                TextInput::make('unit_price')
                    ->label('Preço Unitário')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produto'),
                TextColumn::make('variant.sku')
                    ->label('Variante')
                    ->placeholder('—'),
                TextColumn::make('quantity')
                    ->label('Quantidade'),
                TextColumn::make('unit_price')
                    ->label('Preço Unitário')
                    ->money('BRL'),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (OrderItem $record): float => $record->total)
                    ->money('BRL'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => $this->isOrderEditable()),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => $this->isOrderEditable()),
                DeleteAction::make()
                    ->visible(fn (): bool => $this->isOrderEditable()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => $this->isOrderEditable()),
            ]);
    }

    private function isOrderEditable(): bool
    {
        /** @var Order $order */
        $order = $this->getOwnerRecord();

        return $order->status === OrderStatus::Draft;
    }
}
