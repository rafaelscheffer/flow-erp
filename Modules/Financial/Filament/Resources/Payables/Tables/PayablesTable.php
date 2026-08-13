<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Payables\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Financial\Enums\PayableStatus;
use Modules\Financial\Enums\PaymentMethod;
use Modules\Financial\Models\Payable;

class PayablesTable
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
                TextColumn::make('purchase_order_id')
                    ->label('Pedido')
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PayableStatus $state): string => match ($state) {
                        PayableStatus::Pending => 'warning',
                        PayableStatus::Paid => 'success',
                        PayableStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (PayableStatus $state): string => $state->label()),
                TextColumn::make('paid_at')
                    ->label('Pago em')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(PayableStatus::cases())->mapWithKeys(
                        fn (PayableStatus $case): array => [$case->value => $case->label()],
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Payable $record): bool => $record->status === PayableStatus::Pending),
                Action::make('markAsPaid')
                    ->label('Marcar como Pago')
                    ->color('success')
                    ->requiresConfirmation()
                    ->schema([
                        Select::make('payment_method')
                            ->label('Forma de Pagamento')
                            ->options(collect(PaymentMethod::cases())->mapWithKeys(
                                fn (PaymentMethod $case): array => [$case->value => $case->label()],
                            ))
                            ->required(),
                    ])
                    ->visible(fn (Payable $record): bool => $record->status === PayableStatus::Pending)
                    ->authorize('markAsPaid')
                    ->action(fn (Payable $record, array $data) => $record->update([
                        'status' => PayableStatus::Paid,
                        'paid_at' => now(),
                        'payment_method' => $data['payment_method'],
                    ])),
                Action::make('cancel')
                    ->label('Cancelar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Payable $record): bool => $record->status === PayableStatus::Pending)
                    ->authorize('update')
                    ->action(fn (Payable $record) => $record->update(['status' => PayableStatus::Cancelled])),
            ])
            ->defaultSort('due_date');
    }
}
