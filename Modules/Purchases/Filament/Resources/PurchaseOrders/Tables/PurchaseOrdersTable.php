<?php

declare(strict_types=1);

namespace Modules\Purchases\Filament\Resources\PurchaseOrders\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Modules\Purchases\Actions\ReceivePurchaseOrderAction;
use Modules\Purchases\Enums\PurchaseOrderStatus;
use Modules\Purchases\Models\PurchaseOrder;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nº')
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Fornecedor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Local de Entrega'),
                TextColumn::make('order_date')
                    ->label('Data do Pedido')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PurchaseOrderStatus $state): string => match ($state) {
                        PurchaseOrderStatus::Draft => 'gray',
                        PurchaseOrderStatus::Sent => 'warning',
                        PurchaseOrderStatus::Received => 'success',
                        PurchaseOrderStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (PurchaseOrderStatus $state): string => $state->label()),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (PurchaseOrder $record): float => $record->total)
                    ->money('BRL'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(PurchaseOrderStatus::cases())->mapWithKeys(
                        fn (PurchaseOrderStatus $case): array => [$case->value => $case->label()],
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (PurchaseOrder $record): bool => $record->status === PurchaseOrderStatus::Draft),
                Action::make('send')
                    ->label('Enviar')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (PurchaseOrder $record): bool => $record->status === PurchaseOrderStatus::Draft)
                    ->authorize('update')
                    ->action(fn (PurchaseOrder $record) => $record->update(['status' => PurchaseOrderStatus::Sent])),
                Action::make('receive')
                    ->label('Receber')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Isso irá gerar a entrada de estoque para todos os itens do pedido.')
                    ->visible(fn (PurchaseOrder $record): bool => $record->status === PurchaseOrderStatus::Sent)
                    ->authorize('receive')
                    ->action(fn (PurchaseOrder $record) => app(ReceivePurchaseOrderAction::class)->execute($record, Auth::id())),
                Action::make('cancel')
                    ->label('Cancelar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PurchaseOrder $record): bool => in_array($record->status, [PurchaseOrderStatus::Draft, PurchaseOrderStatus::Sent], true))
                    ->authorize('update')
                    ->action(fn (PurchaseOrder $record) => $record->update(['status' => PurchaseOrderStatus::Cancelled])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
