<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Receivables\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Financial\Enums\PaymentMethod;
use Modules\Financial\Enums\ReceivableStatus;
use Modules\Financial\Models\Receivable;

class ReceivablesTable
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
                TextColumn::make('order_id')
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
                    ->color(fn (ReceivableStatus $state): string => match ($state) {
                        ReceivableStatus::Pending => 'warning',
                        ReceivableStatus::Paid => 'success',
                        ReceivableStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (ReceivableStatus $state): string => $state->label()),
                TextColumn::make('paid_at')
                    ->label('Pago em')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ReceivableStatus::cases())->mapWithKeys(
                        fn (ReceivableStatus $case): array => [$case->value => $case->label()],
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Receivable $record): bool => $record->status === ReceivableStatus::Pending),
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
                    ->visible(fn (Receivable $record): bool => $record->status === ReceivableStatus::Pending)
                    ->authorize('markAsPaid')
                    ->action(fn (Receivable $record, array $data) => $record->update([
                        'status' => ReceivableStatus::Paid,
                        'paid_at' => now(),
                        'payment_method' => $data['payment_method'],
                    ])),
                Action::make('cancel')
                    ->label('Cancelar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Receivable $record): bool => $record->status === ReceivableStatus::Pending)
                    ->authorize('update')
                    ->action(fn (Receivable $record) => $record->update(['status' => ReceivableStatus::Cancelled])),
            ])
            ->defaultSort('due_date');
    }
}
