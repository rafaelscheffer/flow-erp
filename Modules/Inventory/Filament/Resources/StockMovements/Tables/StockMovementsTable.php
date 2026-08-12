<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockMovements\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Inventory\Enums\StockMovementType;
use Modules\Inventory\Models\StockLocation;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (StockMovementType $state): string => $state->label()),
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
                    ->label('Quantidade')
                    ->badge()
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '').$state)
                    ->sortable(),
                TextColumn::make('performedBy.name')
                    ->label('Responsável'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(collect(StockMovementType::cases())->mapWithKeys(
                        fn (StockMovementType $case): array => [$case->value => $case->label()],
                    )),
                SelectFilter::make('stock_location_id')
                    ->label('Local')
                    ->options(StockLocation::query()->pluck('name', 'id')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
