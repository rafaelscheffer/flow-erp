<?php

declare(strict_types=1);

namespace Modules\Financial\Filament\Resources\Accounts\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Modules\Financial\Enums\AccountType;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (AccountType $state): string => $state->label()),
                TextColumn::make('parent.name')
                    ->label('Conta Pai')
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Ativa')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(collect(AccountType::cases())->mapWithKeys(
                        fn (AccountType $case): array => [$case->value => $case->label()],
                    )),
                TernaryFilter::make('is_active')
                    ->label('Ativa'),
            ])
            ->defaultSort('code');
    }
}
