<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockBalances\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Inventory\Models\StockLocation;

class StockBalancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('variant.sku')
                    ->label('Variante')
                    ->placeholder('—'),
                TextColumn::make('location.name')
                    ->label('Local')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Em Estoque')
                    ->badge()
                    ->color(function ($record): string {
                        if ($record->quantity <= 0) {
                            return 'danger';
                        }

                        return $record->quantity <= $record->product->min_stock ? 'warning' : 'success';
                    })
                    ->sortable(),
                TextColumn::make('reserved_quantity')
                    ->label('Reservado')
                    ->sortable(),
                TextColumn::make('available_quantity')
                    ->label('Disponível')
                    ->state(fn ($record): int => $record->quantity - $record->reserved_quantity),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('stock_location_id')
                    ->label('Local')
                    ->options(StockLocation::query()->pluck('name', 'id')),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
