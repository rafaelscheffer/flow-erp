<?php

declare(strict_types=1);

namespace Modules\Administration\Filament\Resources\AuditLogs\Tables;

use App\Enums\AuditEventType;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->default('Sistema')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->formatStateUsing(fn (AuditEventType $state): string => $state->label())
                    ->color(fn (AuditEventType $state): string => $state->color()),
                TextColumn::make('auditable_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable(),
                TextColumn::make('auditable_id')
                    ->label('ID'),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label('Evento')
                    ->options(collect(AuditEventType::cases())->mapWithKeys(
                        fn (AuditEventType $case): array => [$case->value => $case->label()],
                    )),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
