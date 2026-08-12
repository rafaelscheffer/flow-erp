<?php

declare(strict_types=1);

namespace Modules\Sales\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Modules\Sales\Actions\ConfirmSaleAction;
use Modules\Sales\Enums\OrderStatus;
use Modules\Sales\Models\Order;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nº')
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Local de Saída'),
                TextColumn::make('order_date')
                    ->label('Data do Pedido')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::Draft => 'gray',
                        OrderStatus::Confirmed => 'warning',
                        OrderStatus::Invoiced => 'success',
                        OrderStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label()),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (Order $record): float => $record->total)
                    ->money('BRL'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(
                        fn (OrderStatus $case): array => [$case->value => $case->label()],
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Draft),
                Action::make('confirm')
                    ->label('Confirmar')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Isso irá gerar a saída de estoque para todos os itens do pedido.')
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Draft)
                    ->authorize('confirm')
                    ->action(fn (Order $record) => app(ConfirmSaleAction::class)->execute($record, Auth::id())),
                Action::make('invoice')
                    ->label('Faturar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Confirmed)
                    ->authorize('invoice')
                    ->action(fn (Order $record) => $record->update(['status' => OrderStatus::Invoiced, 'invoiced_at' => now()])),
                Action::make('cancel')
                    ->label('Cancelar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Draft)
                    ->authorize('update')
                    ->action(fn (Order $record) => $record->update(['status' => OrderStatus::Cancelled])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
