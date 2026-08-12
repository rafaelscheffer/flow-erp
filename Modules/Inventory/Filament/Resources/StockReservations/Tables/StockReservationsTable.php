<?php

declare(strict_types=1);

namespace Modules\Inventory\Filament\Resources\StockReservations\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Inventory\Enums\StockReservationStatus;
use Modules\Inventory\Models\StockReservation;

class StockReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('variant.sku')
                    ->label('Variante')
                    ->placeholder('—'),
                TextColumn::make('location.name')
                    ->label('Local'),
                TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (StockReservationStatus $state): string => match ($state) {
                        StockReservationStatus::Active => 'warning',
                        StockReservationStatus::Fulfilled => 'success',
                        StockReservationStatus::Released => 'gray',
                    })
                    ->formatStateUsing(fn (StockReservationStatus $state): string => $state->label()),
                TextColumn::make('reservedBy.name')
                    ->label('Reservado por'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(StockReservationStatus::cases())->mapWithKeys(
                        fn (StockReservationStatus $case): array => [$case->value => $case->label()],
                    )),
            ])
            ->recordActions([
                Action::make('release')
                    ->label('Liberar')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (StockReservation $record): bool => $record->status === StockReservationStatus::Active)
                    ->authorize('update')
                    ->action(fn (StockReservation $record) => $record->update(['status' => StockReservationStatus::Released])),
                Action::make('fulfill')
                    ->label('Confirmar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (StockReservation $record): bool => $record->status === StockReservationStatus::Active)
                    ->authorize('update')
                    ->action(fn (StockReservation $record) => $record->update(['status' => StockReservationStatus::Fulfilled])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
